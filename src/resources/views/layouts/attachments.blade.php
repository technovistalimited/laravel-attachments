<?php
/**
 * ATTACHMENTS:
 * Same template for Add, Edit, View.
 *
 * REUSABLE COMPONENT_________________________________________________
 * Before making any changes in this file, specific to your project,
 * please study the module guide first and modify accordingly.
 *
 * DEVELOPER NOTE_____________________________________________________
 * While changing any class and/or ID, please make sure the Javascripts
 * and CSS files are also revised accordingly. Because classes and IDs
 * have business according to various UI and user interactions.
 */
?>

<?php
// ------------------------------------
// VARIABLES.
// Necessary variables for attachments.
// ------------------------------------
$_maximum_upload_size = (int) config('shongjukti.upload_max_size');
$_max_size_in_mb      = round(Shongjukti::bytesToMb($_maximum_upload_size), 2, PHP_ROUND_HALF_UP);
$_default_browse_text = __('shongjukti::messages.browse');

if( isset($attachmentTypes) && ! $attachmentTypes->isEmpty() ) {
    if( isset($attachments) ) {
        $_mode_class = 'attachment-edit';
    } else {
        $_mode_class = 'attachment-add';
    }
} else {
    $_mode_class = 'attachment-view';
}
?>

<section class="attachments {{ $_mode_class }}">

    <?php
    /**
     * ------------------------------------------------------
     * PLACEHOLDER HOOK: attachment_block_head_class
     * ------------------------------------------------------
     *
     * Pass the view page's section heading CSS class from the
     * add/edit/view page using `@section('attachment_block_head_class')`
     * when necessary.
     * ------------------------------------------------------
     */
    ?>
    <h5 class="@yield( 'attachment_block_head_class', 'section-head' )">
        <?php
        /**
         * ------------------------------------------------------
         * PLACEHOLDER HOOK: attachment_block_head
         * ------------------------------------------------------
         *
         * Pass the attachment block head from the view page
         * using `@section('attachment_block_head')` when
         * necessary. Default: 'Attachments'.
         * ------------------------------------------------------
         */
        ?>
        @yield( 'attachment_block_head', __('shongjukti::messages.attachments') )
    </h5>

    @if( isset($attachmentTypes) && ! $attachmentTypes->isEmpty() )

        <?php
        /**
         * -----------------------------------------------------
         * ADD/EDIT TEMPLATE
         *
         * Display the attachments in add/edit mode.
         * -----------------------------------------------------
         */
        ?>

        <?php $_counter = 0; ?>
        @foreach( $attachmentTypes as $attachmentType )

            <?php $attachmentType = Shongjukti::localizeNames($attachmentType); ?>

            <div class="attachment-row {{ $attachmentType->is_required ? 'row-required' : '' }}">
                <div class="row">

                    <?php
                    /**
                     * HIDDEN FIELD:
                     * The Stored Attachment ID.
                     * Required for database query while updating.
                     */
                    ?>
                    <input type="hidden" name="attachments[{{$_counter}}][attachment_id]" value="{{ isset($attachments) && isset($attachments[$attachmentType->id]['id']) ? $attachments[$attachmentType->id]['id'] : '' }}">

                    <div class="col-sm-5">
                        <span class="attachment-counter {{ $attachmentType->is_label_accepted ? 'mt-20' : '' }}">
                            {{ sprintf("%02d", $_counter + 1) }}
                        </span>
                        <div class="attachment-type-group">
                            <div class="{{ $attachmentType->is_label_accepted ? 'small' : 'form-control-static' }}">
                                {{ $attachmentType->name }}
                                @if( ! $attachmentType->is_required )
                                    <span class="badge badge-default badge-secondary">{{ strtolower(__('shongjukti::messages.optional')) }}</span>
                                @endif
                            </div>

                            <?php
                            /**
                             * HIDDEN FIELD:
                             * The Attachment Type ID.
                             */
                            ?>
                            <input type="hidden" name="attachments[{{$_counter}}][attachment_type_id]" value="{{ $attachmentType->id }}">

                            <?php
                            /**
                             * HIDDEN FIELD:
                             * Flag whether custom label accepted or not.
                             */
                            ?>
                            <input type="hidden" name="attachments[{{$_counter}}][is_label_accepted]" value="{{ $attachmentType->is_label_accepted }}">

                            @if( $attachmentType->is_label_accepted )
                                <?php
                                $_attachment_label = isset($attachments) && isset($attachments[$attachmentType->id]['label']) ? $attachments[$attachmentType->id]['label'] : old("attachments[{{$_counter}}][attachment_label]");


                                /**
                                 * ATTACHMENT FIELD: CUSTOM LABEL
                                 * Custom label field when activated.
                                 */
                                ?>
                                <input type="text" class="attachment-label form-control" name="attachments[{{$_counter}}][attachment_label]" value="{{ $_attachment_label }}" {{ $attachmentType->is_required && $attachmentType->is_label_accepted ? 'required' : '' }} placeholder="{{ __('shongjukti::messages.provide_label') }}" autocomplete="off">
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-7">
                        <div class="attachment-file-group {{ $attachmentType->is_required ? 'file-required' : '' }}">
                            <?php
                            // if not mentioned, apply the default file types.
                            $_accepted_extns  = !empty($attachmentType->accepted_extensions) ? $attachmentType->accepted_extensions : (string) config('shongjukti.default_extensions');
                            $_mime_type_array = Shongjukti::mimeTypesFromExtensions($_accepted_extns);
                            $_existing        = isset($attachments) && !empty($attachments[$attachmentType->id]['path']) ? $attachments[$attachmentType->id]['path'] : false;
                            $_mt_class        = $attachmentType->is_label_accepted ? 'mt-20' : '';
                            ?>

                            @if( $_existing )
                                <?php $_stripped_file_name = '...' . substr(basename($_existing), -15); ?>

                                <a href="{{ $_existing }}" class="btn btn-sm btn-default btn-outline-dark btn-view {{$_mt_class}}" target="_blank" rel="noopener">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="4 4 16 16" class="mr-1" fill="#999999" style="display: inline-block;"><path d="M14 5.333v3.333h3.334v10H6.667V5.333H14zM14.667 4H5.333v16h13.334V8l-4-4z"/></svg>
                                    {!! sprintf( __('shongjukti::messages.view_file'), $_stripped_file_name) !!}

                                    <?php
                                    /**
                                     * HIDDEN FIELD:
                                     * Is to Delete.
                                     * Toggled with JavaScripts.
                                     * A MUST to manage deletion of optional files.
                                     */
                                    ?>
                                    <input type="hidden" name="attachments[{{$_counter}}][is_deleted]" class="attachment-is-deleted" value="0">
                                </a>

                                <button type="button" class="btn btn-sm btn-danger btn-file-remove {{$_mt_class}}">
                                    {{ __('shongjukti::messages.remove') }}
                                </button>
                            @endif

                            <?php
                            if ($_existing) {
                                $_required_field = '';
                            } elseif ($attachmentType->is_required) {
                                $_required_field = 'required';
                            } else {
                                $_required_field = '';
                            }
                            $_hidden_class = isset($attachments) && !empty($attachments[$attachmentType->id]['path']) ? 'hide d-none' : '';
                            ?>

                            <div class="btn btn-sm btn-default btn-outline-dark btn-file {{ $_hidden_class }} {{$_mt_class}}" tabindex="-1">
                                <span class="attachment-browse-label">{{ $_default_browse_text }}</span>
                                <?php
                                /**
                                 * ATTACHMENT FIELD: ATTACHMENT FILE
                                 * 'The' Field
                                 */
                                ?>
                                <input type="file" name="attachments[{{$_counter}}][attachment_file]" class="form-control attachment-file" {{ $_required_field }} accept="{{ implode(',', $_mime_type_array) }}">
                            </div>

                            <?php
                            /**
                             * HIDDEN FIELD:
                             * Flag whether the item is required or not.
                             * Necessary to perform delete on optional fields.
                             */
                            ?>
                            <input type="hidden" name="attachments[{{$_counter}}][is_required]" value="{{ $attachmentType->is_required }}">

                        </div>
                    </div>
                </div>
            </div>

            <?php $_counter++; ?>

        @endforeach


        <?php
        /**
         * ------------------------
         * DATA TO JAVASCRIPTS.
         * Attachment Related Data passed to JavaScripts.
         * @var array
         * ------------------------
         */
        $_attachment_js_data  = array(
            'browse_text'     => $_default_browse_text,
            'max_upload_size' => $_maximum_upload_size,
            'file_size_msg'   => sprintf( __('shongjukti::messages.file_size_exceeds'), $_max_size_in_mb ),
            'file_type_msg'   => __('shongjukti::messages.file_type_not_supported')
        );
        ?>
        <script type="text/javascript">
            /* <![CDATA[ */
            var _attachments = {!! json_encode($_attachment_js_data) !!};
            /* ]]> */
        </script>


    @elseif( isset($attachments) && count($attachments) > 0 && ! isset($attachmentTypes) )

        <?php
        /**
         * -----------------------------------------------------
         * VIEW TEMPLATE
         *
         * Display the attachments in view mode.
         * -----------------------------------------------------
         */
        ?>

        <?php $_counter = 1; ?>
        @foreach($attachments as $attachment)

            <?php $attachment = Shongjukti::localizeNames($attachment); ?>

            <div class="attachment-row">
                <div class="row">
                    <?php $_mt_class = empty($attachment->attachment_label) ? '' : 'mt-10'; ?>

                    <div class="col-sm-4 col-xs-8">
                        <span class="attachment-counter {{ $_mt_class }}">
                            {{ sprintf("%02d", $_counter) }}
                        </span>
                        <div class="attachment-type-group">
                            <div class="{{ empty($attachment->attachment_label) ? 'form-control-static' : 'small text-muted' }}">
                                {{ $attachment->name }}
                            </div>
                            @if( !empty($attachment->attachment_label) )
                                {{ $attachment->attachment_label }}
                            @endif
                        </div>
                    </div>

                    @if( !empty($attachment->attachment_path) )
                        <div class="col-sm-7 col-xs-4">
                            <?php $_stripped_file_name = '...' . substr(basename($attachment->attachment_path), -15); ?>

                            <a href="{{ $attachment->attachment_path }}" target="_blank" rel="noopener" class="btn btn-default btn-outline-dark btn-sm {{ $_mt_class }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="4 4 16 16" class="mr-1" fill="#999999" style="display: inline-block;"><path d="M14 5.333v3.333h3.334v10H6.667V5.333H14zM14.667 4H5.333v16h13.334V8l-4-4z"/></svg>
                                {!! sprintf( __('shongjukti::messages.view_file'), $_stripped_file_name) !!}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <?php $_counter++; ?>

        @endforeach

    @else

        <div class="alert alert-info" role="alert">
            {{ __('shongjukti::messages.no_attachment') }}
        </div>

    @endif

</section>
<!-- /.attachments -->
