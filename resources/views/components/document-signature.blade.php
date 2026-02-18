@props([
    'positionKey' => null,
    'label' => '',
    'name' => null,
    'signatureUrl' => null,
    'showImage' => true,
    'style' => 'receipt',
])

@php
    // Only look up config for admin-configurable positions (APPROVING_AUTHORITY, MEEDO_OFFICER)
    $sigData = null;
    $skipLookup = in_array($positionKey, ['CUSTOMER', 'CURRENT_USER', null]);
    if (!$skipLookup && !$name) {
        $sigData = app(\App\Services\DocumentSignatory\DocumentSignatoryService::class)
            ->resolveSignatureData($positionKey);
    }
    $displayName = $name ?? ($sigData['name'] ?? '');
    $resolvedSignatureUrl = $signatureUrl ?? ($sigData['signature_url'] ?? null);
    $positionTitle = $label ?: ($sigData['title'] ?? '');
@endphp

@if($style === 'receipt')
<div class="signature-box">
    @if($resolvedSignatureUrl && $showImage)
    <img src="{{ $resolvedSignatureUrl }}" alt="Signature" style="height: 36px; margin: 0 auto 2px; display: block; object-fit: contain;">
    @endif
    <div style="font-weight: 700; font-size: 9px; text-transform: uppercase; margin-top: 30px; min-height: 14px;">{{ $displayName }}</div>
    <div class="line" style="margin-top: 4px;"></div>
    <div class="label">{{ $positionTitle }}</div>
</div>
@elseif($style === 'application')
<div class="signature-box">
    @if($resolvedSignatureUrl && $showImage)
    <img src="{{ $resolvedSignatureUrl }}" alt="Signature" style="height: 44px; margin: 0 auto; display: block; object-fit: contain;">
    @endif
    <div style="font-weight: 600; font-size: 11px; text-transform: uppercase; margin-top: 40px; min-height: 16px;">{{ $displayName }}</div>
    <div class="line" style="margin-top: 4px;"></div>
    <div class="label">{{ $positionTitle }}</div>
</div>
@elseif($style === 'contract')
<div class="signature-box">
    @if($resolvedSignatureUrl && $showImage)
    <img src="{{ $resolvedSignatureUrl }}" alt="Signature" style="height: 44px; margin: 0 auto; display: block; object-fit: contain;">
    @endif
    <div class="name" style="font-weight: 600; text-transform: uppercase; margin-top: 34px; min-height: 16px;">{{ $displayName }}</div>
    <div class="line" style="margin-top: 4px;"></div>
    <div class="title">{{ $positionTitle }}</div>
</div>
@elseif($style === 'report')
<div class="signature-block">
    <div class="signature-line">
        @if($resolvedSignatureUrl && $showImage)
        <img src="{{ $resolvedSignatureUrl }}" alt="Signature" style="height: 44px; margin: 0 auto 6px; display: block; object-fit: contain;">
        @endif
        <div class="signature-name" style="text-transform: uppercase;">{{ $displayName }}</div>
        <div class="signature-title">{{ $positionTitle }}</div>
    </div>
</div>
@endif
