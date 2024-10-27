=== AdCaptcher ===
Contributors: razvaniacob, razvantirboaca
Tags: captcha, comment, comments, advertising, anti-spam, spam, security, ads, adcaptcher
Requires at least: 2.4
Tested up to: 2.9.1
Stable tag: 1.2.5

AdCaptcher.com enables you to build custom captchas. You need an <a href="http://admin.adcaptcher.com" target="_blank">AdCaptcher account</a> to administrate your custom captchas.

== Description ==

AdCaptcher enables you to build custom captchas. Instead of showing a random string of characters, AdCaptcher lets you upload your own custom images which can be promotional texts, brand names, logos, products or just texts that make sense. We support all languages, not just the latin ones and with our technology the images remain as they are. No distorted or blurry texts.

You need an AdCaptcher account to create profiles for your websites and to administrate your captchas. The images are organized in campaigns which can be scheduled to a certain time period, limited to a certain number of impressions and also can be targeted on your websites.

AdCaptcher also provides an Analytics section where you can keep track of your campaigns. We track the impressions (how many times an image has been seen), the fills (both types: successful or not) and the clicks (premium members can link the images). Basically AdCaptcher is your tool for running captcha advertising campaigns.


[Plugin URI]: (http://admin.adcaptcher.com/plugins/wordpress/)

== Screenshots ==

1. AdCaptcher's captcha on the comment form.

2. The AdCaptcher administrative platform.


== Installation ==

Installing should be a piece of cake and take fewer than five minutes.

1. Extract the zip file into your `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. It will ask you to enter your AdCaptcher.com Activation key, do so.

== Frequently Asked Questions ==

= Troubleshooting if the CAPTCHA form fields and image is not being shown: =

Do this as a test:
Activate the AdCaptcher plugin and temporarily change your theme to the "Wordpress Default" theme.
Does the captcha image show now?
If it does then the theme you are using is the cause.

Your theme must have a `<?php do_action('comment_form', $post->ID); ?>` tag inside your comments.php form. Most themes do.

= Troubleshooting if the CAPTCHA image itself is not being shown: =

By default, the admin will not see the AdCaptcher field. If you click "log out", go look at the comment form and it will be there.

= Is this plugin available in other languages? =

No. We want to add multi-language support in the next versions.

= Can I provide a translation? =

Of course! It will be very gratefully received. 
* If you have any questions, feel free to email me also at razvan.iacob@adcaptcher.com. Thanks!

== Changelog ==

= 1.2.5 =
- (19 Mar 2010) Private key file_exist check removed.

= 1.2.4 =
- (22 Feb 2010) Tabindex to the captcha field added.

= 1.2.3 =
- (26 Jan 2010) Captcha code verification done prior to any other processing, when saving a new comment in the database.

= 1.2.2 =
- (23 Jan 2010) Embeded script moved from footer

= 1.2.1 =
- (22 Jan 2010) Critical update at the protection system 

= 1.2 =
- (21 Jan 2010) Functions wrapped in adcaptcher class

= 1.1 =
- (19 Jan 2010) Css fix: no background for captcha image's link

= 1.0 =
- (21 Dec 2009) Initial Release