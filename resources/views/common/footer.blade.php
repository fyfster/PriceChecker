
<!-- BEGIN FOOTER -->
<div class="page-footer">
    @include('common.copyright')
    <div class="page-footer-tools">
        <span class="go-top">
        <i class="fa fa-angle-up"></i>
        </span>
    </div>
</div>
<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<script src="{{ url('assets/global/plugins/jquery-ui/jquery-ui.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/bootstrap/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/js.cookie.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/jquery.blockui.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}" type="text/javascript"></script>

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="{{ url('assets/global/scripts/app.js') }}" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<!--[if lt IE 9]>
<script src="{{ url('assets/global/plugins/respond.min.js') }}"></script>
<script src="{{ url('assets/global/plugins/excanvas.min.js') }}"></script>
<![endif]-->

<!-- BEGIN THEME LAYOUT SCRIPTS -->
<script src="{{ url('assets/layouts/layout/scripts/layout.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/layouts/layout/scripts/demo.min.js') }}" type="text/javascript"></script>
<script src="{{ url('assets/layouts/global/scripts/quick-nav.min.js') }}" type="text/javascript"></script>
<!-- END THEME LAYOUT SCRIPTS -->

@if(isset($useSelect2))
    <script src="{{ url('assets/global/plugins/select2/js/select2.full.min.js') }}" type="text/javascript"></script>
@endif

@if(isset($useDatatables))
    <script src="{{ url('assets/global/scripts/datatable.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/global/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/global/plugins/datatables/plugins/bootstrap/datatables.bootstrap.js') }}" type="text/javascript"></script>
@endif

@if(isset($useDatePicker))
    <script src="{{ url('assets/global/plugins/bootstrap-datetimepicker/moment-with-locales.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/global/plugins/bootstrap-datetimepicker/bootstrap-datetimepicker.js') }}" type="text/javascript"></script>
@endif

@if(isset($useCalendar))
    <script src="{{ url('assets/global/plugins/calendar/moment-with-locales.min.js') }}" type="text/javascript"></script>
    <script src="{{ url('assets/global/plugins/calendar/fullcalendar.min.js') }}" type="text/javascript"></script>
@endif

@if(isset($useMultiSelect))
    <script src="{{ url('assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js') }}" type="text/javascript"></script>
@endif

@if(isset($useProfile))
    <script src="{{ url('assets/global/scripts/profile.js') }}" type="text/javascript"></script>
@endif

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<!-- END CORE PLUGINS -->
</div>
</body>
<!-- END BODY -->
</html>
