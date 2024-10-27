<?php

namespace App\Helpers;


class Response
{

    public const ACTIVE = 'response.active';
    public const INACTIVE = 'response.in_active';

    public const SUCCESS = 'response.success';
    public const FAILED = 'response.failed';
    public const NOT_AUTHORIZED = 'response.not_authorized';
    public const NOT_AUTHENTICATED = 'response.not_authenticated';
    public const USER_NOT_FOUND = 'response.user_not_found';
    public const NOT_VERIFIED = 'response.not_verified';
    public const NOT_ENABLED = 'response.not_enabled';

    public const REGISTER_SUCCESSFULLY = 'response.register_successfully';

    public const REGISTER_NOT_ALLOWED = 'response.register_not_allowed';
    public const REGISTER_WITH_CONFIRMATION_SUCCESSFULLY = 'response.register_with_confirmation_successfully';

    public const CONFIRMATION_SUCCESSFULLY = 'response.confirmation_successfully';

    public const WRONG_PASSWORD = 'response.wrong_password';
    public const LOGIN_SUCCESSFULLY = 'response.login_successfully';
    public const LOGOUT_SUCCESSFULLY = 'response.logout_successfully';
    public const LOGIN_FAILED = 'response.login_failed';
    public const CODE_FAILED = 'response.code_failed';
    public const MUST_LOGIN = 'response.must_login';

    public const ADDED_SUCCESSFULLY = 'response.added_successfully';
    public const UPDATED_SUCCESSFULLY = 'response.updated_successfully';
    public const DELETED_SUCCESSFULLY = 'response.deleted_successfully';
    public const TRASHED_SUCCESSFULLY = 'response.trashed_successfully';
    public const RESTORED_SUCCESSFULLY = 'response.restored_successfully';
    public const NOT_ALLOWED = 'response.not_allowed';
    public const NOT_FOUND = 'response.not_found';

    public const DELETE_MESSAGE = 'response.delete_message';
    public const DELETE_SUB_MESSAGE = 'response.delete_sub_message';
    public const CONFIRM_TEXT = 'response.confirm_text';
    public const CANCEL_TEXT = 'response.cancel_text';
    public const ITEMS_NOT_FOUND = 'response.items_not_found';

    public const SLUG_NO_SPACES = 'validation.slug_no_spaces';
    public const NOT_AUTHORIZED_TO_DELETE_THIS_ROW = 'response.you_are_not_authorized_to_delete_this_row';
    public const NOT_AUTHORIZED_TO_UPDATE_THIS_ROW = 'response.you_are_not_authorized_to_update_this_row';
    public const NOT_AUTHORIZED_TO_GET_THIS_ROW = 'response.you_are_not_authorized_to_get_this_row';
    public const ERROR_IN_EMAIL_OR_PASSWORD = 'response.There_is_an_error_in_the_email_or_password';
    public const ACCOUNT_IS_NOT_ACTIVATED = 'response.Your_account_is_not_activated_please_contact_the_system_administrator';
    public const PASSWORD_RESET_LINK_SENT = 'response.A_password_reset_link_has_been_sent_to_your_email_address';
    public const TOKEN_NOT_FOUND = 'response.token_not_found';
    public const PASSWORD_CHANGED = 'response.Your_password_has_been_changed';
    public const SUBMITTED_BEFORE = 'response.submitted_before';
    public const CANT_DELETE = 'response.cant_delete';
    public const ADMIN_NOT_FOUND = 'response.admin_not_found';
    public const INVALID_KEY = 'response.invalid_key';
    public const NO_ITEMS = 'response.no_items';
    public const MODEL_NOT_FOUND = 'response.Model_Not_Found';
    public const TIME_IS_OUT = 'response.time_is_out';
    public const NO_DATA = 'response.no_data';
    public const DONE = 'response.done';





    /**
     * @param mixed $message
     * @param null $content
     * @param integer $status
     *
     * @return JsonResponse
     */
    public static function respondSuccess($message, $content = null, $status = 200)
    {
        return response()->json([
            'status' => true,
            'message' => t($message),
            'data' => $content,
        ], $status);
    }

    /**
     * @param mixed $message
     * @param integer $status
     *
     * @return JsonResponse
     */
    public static function respondError($message, $status = 500)
    {
        return response()->json([
            'status' => false,
            'message' => t($message),
            'data' => null,
        ], $status);
    }

    public static function respondFailed($message, $content = null, $status = 500)
    {
        return response()->json([
            'status' => true,
            'message' => t($message),
            'data' => $content,
        ], $status);
    }


    public static function wrongAnswer($message, $content = null, $status = 200)
    {
        return response()->json([
            'status' => false,
            'message' => t($message),
            'data' => $content,
        ], $status);
    }

    public static function respondNotFound($message, $status = 404)
    {
        return response()->json([
            'status' => false,
            'message' => t($message),
            'data' => null,
        ], $status);
    }

}
