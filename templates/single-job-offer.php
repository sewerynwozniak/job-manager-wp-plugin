<?php get_header(); ?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php
    while ( have_posts() ) :
        the_post();
        ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header><!-- .entry-header -->

            <div class="entry-content">
                <?php
                the_content();

                // Display custom fields
                $place = get_post_meta( get_the_ID(), 'place', true );
                $salary = get_post_meta( get_the_ID(), 'salary', true );

                if ( ! empty( $place ) ) {
                    echo '<p><strong>Place:</strong> ' . esc_html( $place ) . '</p>';
                }

                if ( ! empty( $salary ) ) {
                    echo '<p><strong>Salary:</strong> ' . esc_html( $salary ) . '</p>';
                }
                ?>
            </div><!-- .entry-content -->

            <footer class="entry-footer">
                <?php
                // Edit link, categories, tags, etc.
                ?>
            </footer><!-- .entry-footer -->
        </article><!-- #post-<?php the_ID(); ?> -->

        <!-- Application Form -->
        <div class="job-application-form">
            <h2>Apply for this Job</h2>
            <form id="job-application-form" method="post" enctype="multipart/form-data">
                <p>
                    <label for="applicant_name">Your Name:</label>
                    <input type="text" id="applicant_name" name="applicant_name" required>
                </p>
                <p>
                    <label for="applicant_email">Your Email:</label>
                    <input type="email" id="applicant_email" name="applicant_email" required>
                </p>
                <p>
                    <label for="applicant_cv">Upload your CV:</label>
                    <input type="file" id="applicant_cv" name="applicant_cv" accept=".pdf,.doc,.docx" required>
                </p>
                <p>
                    <input type="hidden" name="job_offer_id" value="<?php echo get_the_ID(); ?>">
                    <?php wp_nonce_field('job_application_form', 'job_application_nonce'); ?>
                    <input type="submit" name="submit_application" value="Apply Now">
                </p>
            </form>
        </div>

        <?php
    endwhile; // End of the loop.
    ?>

    </main><!-- #main -->
</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
