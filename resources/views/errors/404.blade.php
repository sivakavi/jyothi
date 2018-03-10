<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
  .center {text-align: center; margin-left: auto; margin-right: auto; margin-bottom: auto; margin-top: auto;}
</style>
<div class="container">
  <div class="row">
    <div class="span12">
      <div class="hero-unit center">
          <h1>Page Not Found <small><font face="Tahoma" color="red">Error 404</font></small></h1>
          <br />
          <p>{{ $exception->getMessage() }}, either contact your admin or try again. Use your browsers <b>Back</b> button to navigate to the page you have prevously come from</p>
          <p><b>Or you could just press this neat little button:</b></p>
          <a href="{{ URL::to('/') }}" class="btn btn-large btn-info"><i class="icon-home icon-white"></i> Take Me Home</a>
        </div>
        <br />  
    </div>
  </div>
</div>
