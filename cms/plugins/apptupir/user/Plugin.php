<?php namespace AppTupir\User;

use AppTupir\User\Classes\Extend\UserLoginApiControllerExtend;
use Backend;
use System\Classes\PluginBase;
use AppTupir\User\Classes\Extend\UserExtend;
use AppTupir\User\Classes\Extend\UserFlagExtend;
use LibUser\UserFlag\Classes\Services\UserFlagService;
use AppTupir\User\Classes\Extend\UserControllerExtend;
use AppTupir\User\Classes\Extend\UserExtendDefaultAssets;

/**
 * User Plugin Information File
 */
class Plugin extends PluginBase
{
    public $require = [
        'RainLab.User'
    ];

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'User',
            'description' => 'No description provided yet...',
            'author'      => 'AppTupir',
            'icon'        => 'icon-leaf'
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return array
     */
    public function boot()
    {
        UserExtend::extendUserResource();
        UserExtend::updateFormFields_addBioField();
        UserExtend::updateFormFields_addSuperUserSwitch();
        UserExtend::addBioAsFillableToUser();

        UserExtend::addFollowingRelationToUser();
        UserExtend::addFollowersRelationToUser();
        UserExtend::addLikesRelationToUser();
        UserExtend::addBookmarksRelationToUser();
        UserExtend::addSharesRelationToUser();
        UserExtend::addPlaysRelationToUser();
        UserExtend::addVisitsRelationToUser();
        UserExtend::addCatchphraseRelationToUser();

        UserExtend::addUUIDToUser();
        UserExtend::addIsPublishedScope();
        UserExtend::addIsPublishedAsFillable();
        UserExtend::onScopeCanSee_filterPublished();
        UserExtend::deleteUserFlags_onUserDelete();
        UserExtend::beforeShowCatchphrase_checkPublished();
        UserExtend::addCatchphrasesCountToColumns();
        UserExtend::addCatchphrasesCountToResource();
        UserExtend::updateListColumns_addIsPublishedSwitch();
        UserExtend::updateListColumns_removeSurname();
        UserExtend::updateFormFields_addIsPublishedSwitch();
        UserExtend::setMailTemplateForForgottenPassword();

        UserExtendDefaultAssets::beforeSave_setDefaultAvatar();

        UserLoginApiControllerExtend::enableUsernameAuth();

        UserFlagExtend::addUserToAliasesConfig();
        UserFlagExtend::softDeleteUserFlagsOnUserDeleteAndRestore();
        UserFlagService::addTypeStatusToResource('libuser.profile.profile.beforeReturnResource', 'follow', 'is_followed_by_active_user');
    }
}
