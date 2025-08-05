<x-mail::message>
# Order Confirmation - Thank You!

Dear {{ $order->customer_name ?? 'Valued Customer' }},

Thank you for your order! We're excited to confirm that we've received your order and it's being processed.

@if($successMessage)
## Special Message
{{ $successMessage }}
@endif

## Order Details
**Order ID:** #{{ $order->id }}  
**Order Date:** {{ $order->created_at->format('F j, Y \a\t g:i A') }}  
**Total Amount:** ${{ number_format($order->total_amount, 2) }}

## Shipping Information
**Name:** {{ $order->customer_name }}  
**Email:** {{ $order->customer_email }}  
**Phone:** {{ $order->customer_phone }}  
**Address:** {{ $order->shipping_address }}

@php
    $hasAttachments = collect($orderItems)->pluck('product.attachment')->filter()->isNotEmpty();
    $hasProductNotes = collect($orderItems)->filter(function($item) { 
        return !empty($item->product->success_mail); 
    })->isNotEmpty();
@endphp

@if($hasAttachments || $hasProductNotes)
## 🎁 Special Features Included

@if($hasAttachments)
✅ **Digital Downloads:** Your order includes downloadable files  
@endif
@if($hasProductNotes)
✅ **Special Messages:** Personalized notes from our team  
@endif

@if($hasAttachments)
*📥 Scroll down to see your download links and file details!*
@endif
@endif

## Items Ordered
@foreach($orderItems as $item)
- **{{ $item->product->name }}**  
  Quantity: {{ $item->quantity }}  
  Price: ${{ number_format($item->price, 2) }}  
  Subtotal: ${{ number_format($item->quantity * $item->price, 2) }}
  @if($item->product->attachment)
  
  🎁 **Includes Digital Content:** {{ basename($item->product->attachment) }}
  @endif
  @if($item->product->success_mail && $item->product->success_mail !== $successMessage)
  
  💬 **Special Note:** {{ $item->product->success_mail }}
  @endif
  
@endforeach

@if(collect($orderItems)->pluck('product.attachment')->filter()->isNotEmpty())
## � Your Digital Files

Your order includes digital content! Here are the files available for download:

@if(isset($attachmentDetails) && count($attachmentDetails) > 0)
@foreach($attachmentDetails as $attachment)
---

### 📄 {{ $attachment['filename'] }}
**Product:** {{ $attachment['product_name'] }}  
**File Size:** {{ $attachment['size'] }}  

<x-mail::button :url="$attachment['download_url']">
Download {{ $attachment['filename'] }}
</x-mail::button>

@endforeach
@else
@foreach($orderItems as $item)
@if($item->product->attachment)
---

### 📄 {{ basename($item->product->attachment) }}
**Product:** {{ $item->product->name }}  

<x-mail::button :url="url('storage/' . $item->product->attachment)">
Download File
</x-mail::button>

@endif
@endforeach
@endif

---

**Important Notes:**
- Download links are active for 30 days from the order date
- Files are also attached to this email for your convenience
- Contact us if you have any issues downloading your files

@endif

We'll send you another email with tracking information once your order ships.


<x-mail::button :url="url('/')">
Continue Shopping
</x-mail::button>

If you have any questions about your order, please don't hesitate to contact us.

Thanks for choosing us!<br>
{{ config('app.name') }}
</x-mail::message>
