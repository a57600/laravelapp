<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
  <title>Cinema Paradiso</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />
  <link rel="stylesheet" href="{{ asset('css/stylesheet.css') }}">
  <link rel="shortcut icon" type="image/x-icon" href="{{ asset('imagens/cppar2.ico') }}" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

</head>
<body>

<nav class="navbar navbar-default">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">MoviGeek</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
         <a class="navbar-brand" href="{{ route('store') }}"><span class="hideable glyphicon glyphicon-film""></span> CinemaParadiso</a>
        </div>


        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="{{ route('store/register') }}"><span class="hideable glyphicon glyphicon-registration-mark"></span> Register</a></li>
            <li><a href="{{ route('store') }}"><span class="hideable glyphicon glyphicon-home""></span> Home</a></li>
            
            <li id="nav-more" class="dropdown hidden"><!-- the 'more' dropdown item, originally hidden -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">MORE <span class="caret"></span></a>
              <ul class="dropdown-menu">
              </ul>
            </li>
          </ul>
        </div>
        <!--/.nav-collapse -->
      </div>
      <!--/.container-fluid -->
    </nav>





<div class="signup-form"> 
    <form action="{{ route('login_action') }}" method="post">
      {{csrf_field()}}
      <div class="alert alert-primary text-center" role="alert">
        {{$MESSAGE}}
        @if(count($errors))
               <div class="section">             
                   @foreach($errors->all() as $error)
                       <p>{{$error}}</p>
                   @endforeach
               </div><br />
               @endif
    </div>
    <h2>Login</h2>
        <div class="form-group">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-paper-plane"></i></span>
        <input type="email" class="form-control" name="email" placeholder="Email" @if($errors->has('email')) value="" @else value="{{ old('email') }}" @endif>
      </div>
        </div>
    <div class="form-group">
      <div class="input-group">
        <span class="input-group-addon"><i class="fa fa-lock"></i></span>
        <input type="password" class="form-control" name="password" placeholder="Password" @if($errors->has('password')) value="" @else value="{{ old('password') }}" @endif>
      </div>
      <input type="checkbox" name="autologin" value="1">Remember me?
        </div>     
    <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block btn-lg">Login</button>
             <a class="align-center" href="password_reset.php">Forgot password?</a>
        </div>
    <p class="small text-center">Haven't got an account?<br><a href="#">Register now!</a></p>
    <input type="reset" value =" Clear  " class="small text-right">
    </form>
</div>

<footer class="container-fluid text-center">
  <p>Powered by Diogo Martins</p>
</footer>

</body>
</html>
