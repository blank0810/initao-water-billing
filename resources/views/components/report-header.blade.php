<!-- Reusable Report Header Component - Always Light Mode (White Background, Black Text) -->
<div class="header" style="background-color: #ffffff !important; color: #000000 !important;">
    <div class="header-logo">
        <img src="{{ asset('images/logo.png') }}" alt="MEEDO Logo">
    </div>
    <div class="republic" style="color: #000000 !important;">Republic of the Philippines</div>
    <h1 style="color: #000000 !important;">{{ $title ?? 'Initao Municipal Economic Development Office' }}</h1>
    <div class="subtitle" style="color: #333333 !important;">{{ $subtitle ?? 'Water Supply and Sewerage System' }}</div>
    @if(isset($address) && $address)
        <div class="address" style="color: #333333 !important;">{{ $address }}</div>
    @endif
</div>
