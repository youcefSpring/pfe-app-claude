@if(Session::has('error'))
<div class="alert alert-danger" role="alert" id="error-alert">

   {{  Session::get('error') }}
</div>
@endif

<script>
    setTimeout(function() {
         var alert = document.getElementById('error-alert');
         if(alert) alert.style.display = 'none';
    }, 5000);
</script>

