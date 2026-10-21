
<?php
/**
 * Template Part: PDS Newsletter Banner
 */
?>

<section class="pds-contact-newsletter"
         aria-labelledby="pds-contact-newsletter-title">

    <div class="pds-contact-newsletter-inner">

        <h2 id="pds-contact-newsletter-title"
            class="pds-contact-newsletter-heading">

            <?php esc_html_e('Subscribe To', 'jobscout'); ?>
            <br>
            <?php esc_html_e('Our Newsletter', 'jobscout'); ?>

        </h2>

        <form class="pds-contact-newsletter-form" onsubmit="return false;">

            <label class="pds-contact-newsletter-field">

                <span class="pds-contact-newsletter-icon" aria-hidden="true">
                    <svg width="20" height="16" viewBox="0 0 20 16" fill="currentColor">
                        <path d="M18 0H2C0.9 0 0.01 0.9 0.01 2L0 14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2C20 0.9 19.1 0 18 0ZM18 4L10 9L2 4V2L10 7L18 2V4Z"/>
                    </svg>
                </span>

                <input
                    type="email"
                    name="contact_newsletter_email"
                    placeholder="<?php esc_attr_e('Input your email address', 'jobscout'); ?>"
                    required
                >

            </label>

            <button type="button" class="pds-contact-newsletter-btn">
                <?php esc_html_e('SUBSCRIBE', 'jobscout'); ?>
            </button>

        </form>

    </div>

</section>
