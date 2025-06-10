@extends('app.website.website-layouts.main')
@section('content')

<section class="page_banner banner_bg">
    <div class="container">
        <div class="banner_txt">
            <h1>Privacy Policy</h1>
            <div class="breadcrumb">
                <a href="shop.html">Shop</a>
                <span>/</span>
                <span>Privacy Policy</span>
            </div>
        </div>
    </div>
</section>
<section class="content_section">
    <div class="container">
        <h5>{{ t('privacy.Who_we_are') }}</h5>
        <p>{{ t('privacy.Our_website_address') }} https://www.compraspesa.it/</p>
        <h5>{{ t('privacy.What_personal_data') }}</h5>

        <h5>{{ t('privacy.Comments') }}</h5>
        <p>{{ t('privacy.When_visitors_leave_comments') }}</p>
        <p>{{ t('privacy.Anonymous_string') }}</p>

        <h5>{{ t('privacy.Media') }}</h5>
        <p>{{ t('privacy.If_you_upload_images') }}</p>

        <h5>{{ t('privacy.Contact_forms') }}</h5>
        <p>{{ t('privacy.If_you_send_us_a_message') }}</p>

        <h5>{{ t('privacy.Cookies') }}</h5>
        <p>{{ t('privacy.If_you_leave_a_comment') }}</p>
        <p>{{ t('privacy.If_you_visit_our_login_page') }}</p>
        <p>{{ t('privacy.When_you_log_in') }}</p>
        <p>{{ t('privacy.If_you_edit_or_publish_an_article') }}</p>

        <h5>{{ t('privacy.Embedded_content_from_other_websites') }}</h5>
        <p>{{ t('privacy.Articles_on_this_site_may_include_embedded_content') }}</p>
        <p>{{ t('privacy.These_websites_may_collect_data') }}</p>

        <h5>{{ t('privacy.Who_we_share_your_data_with') }}</h5>
        <h5>{{ t('privacy.Google_Analytics') }}</h5>
        <p>{{ t('privacy.Google_Analytics_description') }}</p>

        <h5>{{ t('privacy.Facebook_Analytics') }}</h5>
        <p>{{ t('privacy.Facebook_Analytics_description') }}</p>

        <h5>{{ t('privacy.Google_Ads') }}</h5>
        <p>{{ t('privacy.Google_Ads_description') }}</p>

        <h5>{{ t('privacy.How_long_do_we') }}</h5>
        <p>{{ t('privacy.If_you_leave_a_comment') }}</p>
        <p>{{ t('privacy.For_users_that_register') }}</p>
        <p>{{ t('privacy.To_find_out_the_retention_period') }}</p>

        <h5>{{ t('privacy.What_rights_do_you_have_over_your_data') }}</h5>
        <p>{{ t('privacy.If_you_have_an_account') }}</p>

        <h5>{{ t('privacy.Where_we_send_your_data') }}</h5>
        <p>{{ t('privacy.Visitor_comments_may_be_checked') }}</p>

        <h5>{{ t('privacy.contact_information') }}</h5>
        <p>{{ t('privacy.contact_information_description') }}</p>
        <h5>{{ t('privacy.Additional_information') }}</h5>
        <h5>{{ t('privacy.How_we_protect_your_data') }}</h5>
        <p>{{ t('privacy.We_take_the_security_of_your_data') }}</p>
    </div>
</section>

@endsection