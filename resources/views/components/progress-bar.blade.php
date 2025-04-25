<div class="progress {{ $class }}">
  @php
      $progressBarClass = 'bg-gradient-success';
      if ($percentage < 50) {
          $progressBarClass = 'bg-gradient-danger';
      } elseif ($percentage >= 50 && $percentage < 80) {
          $progressBarClass = 'bg-gradient-warning';
      }
  @endphp

  <div class="progress-bar {{ $progressBarClass }}" role="progressbar" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $percentage }}%;"></div>
</div>
