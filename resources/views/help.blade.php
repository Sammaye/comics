@extends('layouts.app')
@section('title', __('Get Help'))
@section('container', 'container-help')
@section('content')
<div class="help-container">
    <div class="head">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col">
                    <h1 class="display-4">{{ __('Help & FAQs') }}</h1>
                    <p class="lead">{!! __(
                        'Below are a list of FAQs, failing that you can <a href=":href">email me directly</a>.',
                        ['href' => 'mailto:' . e(config('app.support_email'))]
                    ) !!}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container pb-4">
        <div class="row justify-content-center">
            <nav class="nav flex-column col-sm-12">
                <li class="nav-item">
                    <a href="#what-is-site" class="nav-link">What is this site?</a>
                </li>
                <li class="nav-item">
                    <a href="#is-it-free" class="nav-link">Is it free?</a>
                </li>
                <li class="nav-item">
                    <a href="#add-comics" class="nav-link">What can I do to get a comic I like put into my email?</a>
                </li>
                <li class="nav-item">
                    <a href="#why" class="nav-link">Why did you create this site?</a>
                </li>
                <li class="nav-item">
                    <a href="#cookies" class="nav-link">Does this site use cookies?</a>
                </li>
                <li class="nav-item">
                    <a href="#profit" class="nav-link">Does this site earn money?</a>
                </li>
                <li class="nav-item">
                    <a href="#dmca" class="nav-link">I want to sue/DMCA you!</a>
                </li>
                <li class="nav-item">
                    <a href="#terms" class="nav-link">Terms & Conditions</a>
                </li>
            </nav>
            <div class="col-sm-34 offset-md-2">

                <div class="faq-item">
                    <span id="what-is-site" class="head-anchor"></span>
                    <h3>What is this site?</h3>
                    <p>This site is a comic distribution service I made for my own personal use which I have decided to
                        release to the general internet community.</p>
                    <p>It allows you batch your daily comics across the internet and have them sent
                        directly to your inbox.</p>
                </div>

                <div class="faq-item">
                    <span id="is-it-free" class="head-anchor"></span>
                    <h3>Is it free?</h3>
                    <p>Yes.</p>
                </div>

                <div class="faq-item">
                    <span id="add-comics" class="head-anchor"></span>
                    <h3>What can I do to get a comic I like put into my email?</h3>
                    <p><a href="mailto:{{ config('app.support_email') }}?subject=Add A Comic">You can email me at
                            my public inbox</a> with the comic name and homepage URL.</p>
                    <p>There may be some comics that, due to their setup, cannot be scraped. You will be notified in this case.</p>
                </div>

                <div class="faq-item">
                    <span id="why" class="head-anchor"></span>
                    <h3>Why did you create this site?</h3>
                    <p>Personal use.</p>
                    <p>I am a big fan of certain comics:- Dilbert, Garfield, US Acres and xkcd, to name a few. When I tried
                        to "subscribe" I found that either they didn't have a function
                        to do so or it placed a hundred emails in my inbox.</p>
                    <p>Many sites also implement extremely intrusive advertising. GoComics is one
                        which would physically disjoint and shift your screen.
                        I also suffer virus warnings on Dilbert regularly from their popup ads.</p>
                    <p>I do also pay for subscriptions to my favourite comics, this is just my preferred medium.</p>
                </div>

                <div class="faq-item">
                    <span id="cookies" class="head-anchor"></span>
                    <h3>Does this site use cookies?</h3>
                    <p>Yes.</p>
                    <p>The cookies used on this site are:</p>
                    <ul>
                        <li>login cookies</li>
                        <li>analytical cookies</li>
                        <li>third party cookies for example: YouTube</li>
                    </ul>
                    <p>Analytical cookies are unavoidable. They are still the only reliable way to provide analytics for
                        webmasters like myself. They help me to make sure I give you a decent experience.</p>
                    <p>A cookie will never be used to hold personal information about you.</p>
                </div>

                <div class="faq-item">
                    <span id="profit" class="head-anchor"></span>
                    <h3>Does this site earn money?</h3>
                    <p>No, this site is non-profit. It makes no money from its services.</p>
                </div>

                <div class="faq-item">
                    <span id="dmca" class="head-anchor"></span>
                    <h3>I want to sue/DMCA you!</h3>
                    <p>You <a href="mailto:{{ config('app.support_email') }}?subject=DMCA or Suing">can email me about it</a>.
                    </p>
                </div>

                <div class="faq-item faq-tac">
                    <span id="terms" class="head-anchor"></span>
                    <h3>Terms & Conditions</h3>
                    <h5>Your Email Address</h5>
                    <p>When you sign upto this service you agree to giving me your email address and that I may hold it in
                        my database and send you emails.</p>
                    <p>The emails you agree to receive are that of your subscription.</p>
                    <p>If I decide to change the emails you receive (say, to add a newsletter about really awesome news
                        stuff) I am obliged give you prior
                        notice and the ability to opt-out/unsubscribe from these communications.</p>
                    <h5>Cancelling</h5>
                    <p>You can cancel at any time however, you must allow me time to run a script to scrub you from my
                        database fully, the bigger a user you are the longer it will take.</p>
                    <p>This process should not take more than 48 hours however, if after that time you still have not been
                        deleted please
                        <a href="mailto:{{ config('app.support_email') }}?subject=Account Not Deleted">let me know via email</a>
                        and I will endeavour to solve the problem.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
