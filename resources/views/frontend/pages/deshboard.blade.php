
@extends('frontend.layouts.app') 
@section('content') 

<style>
    nav.woocommerce-MyAccount-navigation ul {
  list-style-type: none;
  padding-left: 0;
  font-size: 17px;
  line-height: 26px;
}
ol, ul {
  margin-bottom: 20px;
  padding-left: 0;
  margin-top: 0;
}

nav.woocommerce-MyAccount-navigation ul li.is-active {
  background-color: rgba(0,0,0,.1);
}
nav.woocommerce-MyAccount-navigation ul li {
  padding: 8px 20px;
  background-color: rgba(0,0,0,.05);
  border-bottom: 1px solid rgba(0,0,0,.05);
  list-style: none;
}
ul li, ol li {
  /* color: #666; */
}
ul li {
  list-style: square inside none;
}
ol li, ul li {
  line-height: 24px;
  list-style-position: inside;
}
nav.woocommerce-MyAccount-navigation ul li:not(.is-active):hover {
  background-color: rgba(0,0,0,.07);
}
nav.woocommerce-MyAccount-navigation ul li {
  padding: 8px 20px;
  background-color: rgba(0,0,0,.05);
  border-bottom: 1px solid rgba(0,0,0,.05);
  list-style: none;
}
ul li, ol li {
  /* color: #666; */
}
</style>

<div id="main-module-container">
  <div class="slideshow-wrapper main-slideshow wd_wide" style="min-height: 48px;">
    <div class="slideshow-sub-wrapper"></div>
  </div>
  <div class="breadcrumb-title-wrapper">
    <div class="breadcrumb-title">
      <h1 class="heading-title page-title">আমার অ্যাকাউন্ট</h1>
    </div>
  </div>
  <div id="container" class="page-template default-template wd_wide">
    <div id="content" class="container" role="main">
      <div id="main">
        <div id="container-main" class="col-sm-24">
          <div class="main-content">
            <article id="post-9" class="post-9 page type-page status-publish hentry">
              <div class="entry-content-post">
                <div class="woocommerce">
                  <div class="row">
                    <div class="col-sm-5">
                      @include('frontend.layouts.my_account_side_nave')
                    </div>
                    <div class="col-sm-1"></div>
                    <div class="col-sm-18 my-account-dashboard">
                      <div class="woocommerce-notices-wrapper"></div>
                      <p> আসসালামু আলাইকুম <strong>{{explode('@', auth()->user()->email)[0]}}</strong> (যদি আপনি <strong>{{explode('@', auth()->user()->email)[0]}}</strong> না হয়ে থাকেন তাহলে <a href="javascript:void(0)" onclick="document.getElementById('logoutId2').submit()">লগ অউট করুন</a>) </p>
                      <form id="logoutId2" action="{{route('customerLogout')}}" method="post" style="display:none;">
                        @csrf
                      </form>
                      <p syle="display:none;"> From your account dashboard you can view your <a href="{{route('customers_orders')}}">recent orders</a>, manage your <a href="javascript:void(0)">shipping and billing addresses</a>, and <a href="{{route('customers_profile')}}">edit your password and account details</a>. </p>
                    </div>
                  </div>
                </div>
              </div>
              <footer class="entry-meta"></footer>
            </article>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection