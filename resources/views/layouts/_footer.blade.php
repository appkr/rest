<footer>
  <ul class="list-inline pull-right">
    <li>{!! icon('locale', 'icon') !!}</li>
    @foreach ([ 'en' => 'English', 'ko' => '한국어' ] as $locale => $language)
      <li {{ ($locale == $currentLocale) ? 'class="active"' : '' }}>
        <a href="{{ route('global.locale', ['locale' => $locale, 'return' => urlencode(Request::fullUrl())]) }}">{{$language}}</a>
      </li>
    @endforeach
  </ul>
  <div>
    &copy; {{ date('Y') }} &nbsp;
    <a href="{{ config('app.url') }}">{{ config('project.name') }}</a>
    by <a href="mailto:{{ config('project.authors.0.email') }}">{{ config('project.authors.0.name') }}</a>
  </div>
</footer>

<div>
  <a id="back-to-top" href="#" class="btn btn-sm btn-danger back-to-top" role="button" title="Top">
    {!! icon('top') !!}
  </a>
</div>