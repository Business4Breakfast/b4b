<div class="footer">
    <div class="pull-right">
        {{__('app.footer_slogan')}}
    </div>
    <div>
        <strong>{{__('app.footer_copyright')}}</strong> {{app_name()}} &copy; 2013-{{ Carbon\Carbon::now()->format('Y') }}
    </div>
</div>

