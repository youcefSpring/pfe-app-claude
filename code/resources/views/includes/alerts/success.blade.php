@if(Session::has('success'))
<div class="alert alert-success" role="alert" id="success-alert">
    {{  Session::get('success') }}
</div>
<script>
     setTimeout(function() {
          var alert = document.getElementById('success-alert');
          if(alert) alert.style.display = 'none';
     }, 5000);
</script>
@endif
