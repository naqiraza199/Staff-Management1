 @php
    $client = $getRecord();
@endphp

 <style>
    .placeholder-text {
  color: #535353 !important;
  font-size: 12px;
}
 </style>
 <div class="info-section">
                     <div class="info-row">
                        <strong>Private Info:</strong>
                    </div>
                    
                    <div class="info-row">
                    <span class="placeholder-text">{{ $client->private_info ?? '—' }}</span>
                    </div>
                    <br>
                    <div class="info-row">
                        <strong>Review Date:</strong>
                    </div>
                        <span class="placeholder-text">{{ $client->review_date ?? '—' }}</span>

                
                </div>