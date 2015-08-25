@if (session()->has('flash_notification.message'))
  @if (session()->has('flash_notification.overlay'))
    @include('flash::modal', ['modalClass' => 'flash-modal', 'title' => session('flash_notification.title'), 'body' => session('flash_notification.message')])
  @else
    <div class="alert alert-{{ session('flash_notification.level') }} alert-dismissible {{ session()->has('flash_notification.important') ? 'flash-important' : '' }} flash-message" role="alert">
      @if (session()->has('flash_notification.important'))
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">Close</span>
        </button>
      @endif

      {!! session('flash_notification.message') !!}
    </div>
  @endif
@endif
