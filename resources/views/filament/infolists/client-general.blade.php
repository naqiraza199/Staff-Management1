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
                        <strong>General Information:</strong>
                    </div>
                    <br>
                    <hr>
                     <div class="info-row">
                        <strong>Need to know information:</strong>
                    </div>
                    <hr>
                    <div class="info-row">
                        <span class="placeholder-text">{{ $client->need_to_know_information ?? '—' }}</span>
                    </div>
                    <br><hr>
                    <div class="info-row">
                        <strong>Useful Information:</strong>
                    </div><hr>
                    <span class="placeholder-text">{{ $client->useful_information ?? '—' }}</span>
                
                </div>