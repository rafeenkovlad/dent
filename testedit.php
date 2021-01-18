<div class="block-editor">
    <h1 class="screen-reader-text hide-if-no-js"><?php echo esc_html( $title ); ?></h1>
    <div id="editor" class="block-editor__container hide-if-no-js"></div>
    <div id="metaboxes" class="hidden">
        <?php the_block_editor_meta_boxes(); ?>
    </div>

    <?php // JavaScript is disabled. ?>
    <div class="wrap hide-if-js block-editor-no-js">
        <h1 class="wp-heading-inline"><?php echo esc_html( $title ); ?></h1>
        <div class="notice notice-error notice-alt">
            <p>
                <?php
                $message = sprintf(
                /* translators: %s: Classic Editor plugin URL */
                    __( 'The block editor requires JavaScript. Please enable JavaScript in your browser settings, or try the <a href="%s">Classic Editor plugin</a>.' ),
                    __( 'https://wordpress.org/plugins/classic-editor/' )
                );

                /**
                 * Filters the message displayed in the block editor interface when JavaScript is
                 * not enabled in the browser.
                 *
                 * @since 5.0.3
                 *
                 * @param string  $message The message being displayed.
                 * @param WP_Post $post    The post being edited.
                 */
                echo apply_filters( 'block_editor_no_javascript_message', $message, $post );
                ?>
            </p>
        </div>
    </div>
</div>