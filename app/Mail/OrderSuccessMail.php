<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $orderItems;
    public $attachmentPath;
    public $successMessage;
    public $attachmentDetails;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $attachmentPath = null, $successMessage = null)
    {
        $this->order = $order;
        $this->orderItems = $order->orderItems()->with('product')->get();
        $this->attachmentPath = $attachmentPath;
        $this->successMessage = $successMessage;
        
        // Prepare attachment details for email body
        $this->prepareAttachmentDetails();
    }
    
    /**
     * Prepare attachment details for display in email body
     */
    private function prepareAttachmentDetails()
    {
        $this->attachmentDetails = [];
        
        foreach ($this->orderItems as $item) {
            if ($item->product && $item->product->attachment) {
                $filePath = storage_path('app/public/' . $item->product->attachment);
                if (file_exists($filePath)) {
                    $this->attachmentDetails[] = [
                        'product_name' => $item->product->name,
                        'filename' => basename($item->product->attachment),
                        'size' => $this->formatFileSize(filesize($filePath)),
                        'download_url' => url('storage/' . $item->product->attachment),
                        'file_path' => $filePath
                    ];
                }
            }
        }
    }
    
    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Order Confirmation - Thank You for Your Purchase!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.order-success',
            with: [
                'order' => $this->order,
                'orderItems' => $this->orderItems,
                'attachmentPath' => $this->attachmentPath,
                'successMessage' => $this->successMessage,
                'attachmentDetails' => $this->attachmentDetails,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        
        if ($this->attachmentPath && file_exists($this->attachmentPath)) {
            $attachments[] = Attachment::fromPath($this->attachmentPath);
        }
        
        return $attachments;
    }
}
