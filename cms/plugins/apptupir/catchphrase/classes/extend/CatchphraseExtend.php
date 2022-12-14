<?php namespace AppTupir\Catchphrase\Classes\Extend;

use RainLab\User\Models\User;
use LibChat\Comments\Models\Comment;
use LibUser\UserApi\Facades\JWTAuth;
use LibUser\UserFlag\Models\UserFlag;
use Illuminate\Support\Facades\Event;
use AppTupir\Catchphrase\Models\Catchphrase;

class CatchphraseExtend
{
    public static function addLikesRelationToCatchphrase()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->morphMany['likes'] = [
                UserFlag::class,
                'name'       => 'flaggable',
                'conditions' => 'type = "like"'
            ];
        });
    }

    public static function addPlaysRelationToCatchphrase()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->morphMany['plays'] = [
                UserFlag::class,
                'name'       => 'flaggable',
                'conditions' => 'type = "play"'
            ];
        });
    }

    public static function addVisitsRelationToCatchphrase()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->morphMany['visits'] = [
                UserFlag::class,
                'name'       => 'flaggable',
                'conditions' => 'type = "visit"'
            ];
        });
    }

    public static function addBookmarksRelationToCatchphrase()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->morphMany['bookmarks'] = [
                UserFlag::class,
                'name'       => 'flaggable',
                'conditions' => 'type = "bookmark"'
            ];
        });
    }

    public static function addSharesRelationToCatchphrase()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->morphMany['shares'] = [
                UserFlag::class,
                'name'       => 'flaggable',
                'conditions' => 'type = "share"'
            ];
        });
    }

    public static function beforeDelete_deletePlaysLikesBookmarksShares()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->bindEvent('model.beforeDelete', function () use ($catchphrase) {
                if ($catchphrase->forceDeleting) {
                    $catchphrase->plays()->forceDelete();
                    $catchphrase->visits()->forceDelete();
                    $catchphrase->likes()->forceDelete();
                    $catchphrase->bookmarks()->forceDelete();
                    $catchphrase->shares()->forceDelete();
                } else {
                    $catchphrase->plays()->delete();
                    $catchphrase->visits()->delete();
                    $catchphrase->likes()->delete();
                    $catchphrase->bookmarks()->delete();
                    $catchphrase->shares()->delete();
                }
            });
        });
    }

    public static function afterRestore_restorePlaysVisitsLikesBookmarksShares()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->bindEvent('model.afterRestore', function () use ($catchphrase) {
                $catchphrase->plays()->restore();
                $catchphrase->visits()->restore();
                $catchphrase->likes()->restore();
                $catchphrase->bookmarks()->restore();
                $catchphrase->shares()->restore();
            });
        });
    }

    public static function updateResource_addLikesBookmarksSharesCommentsCount()
    {
        Event::listen('apptupir.catchphrase.catchphrase.beforeReturnResource', function(&$response, Catchphrase $catchphrase) {
            $response['likes'] = UserFlag::where([
                'flaggable_id'   => $catchphrase->id,
                'flaggable_type' => Catchphrase::class,
                'type'          => 'like',
                'value'         => 1
            ])->count();

            $response['bookmarks'] = UserFlag::where([
                'flaggable_id'   => $catchphrase->id,
                'flaggable_type' => Catchphrase::class,
                'type'          => 'bookmark',
                'value'         => 1
            ])->count();

            $response['shares'] = UserFlag::where([
                'flaggable_id'   => $catchphrase->id,
                'flaggable_type' => Catchphrase::class,
                'type'          => 'share'
            ])->count();

            $response['comments'] = Comment::where([
                'commentable_id' => $catchphrase->id,
                'creatable_type' => User::class
            ])->count();
        });
    }

    public static function updateResource_addScore()
    {
        Event::listen('apptupir.catchphrase.catchphrase.beforeReturnResource', function (&$response, $resource) {
            $response['score'] = $resource->score;
        });
    }

    public static function addUserHasAccessScope()
    {
        Catchphrase::extend(function (Catchphrase $catchphrase) {
            $catchphrase->addDynamicMethod('scopeUserHasAccess', function ($query) use ($catchphrase) {
                $user = JWTAuth::getUser();

                $query->whereHas('user', function ($query) {
                    return $query->isPublished();
                });

                if ($user->is_superuser) {
                    return $query;
                }

                $blockedUsersIds = $user->blockedUsers()->get()->pluck('id');

                $query->whereHas('user', function ($query) use ($blockedUsersIds) {
                    return $query->whereNotIn('id', $blockedUsersIds);
                });

                return $query;
            });
        });
    }

    public static function bindEvent_createVisitFlagWhenSpecificCatchphraseIsRequested()
    {
        Event::listen('apptupir.catchphrase.action.show', function(Catchphrase $catchphrase) {

            $user = JWTAuth::getUser();

            if (!$user || !$catchphrase->is_published) {
                return;
            }

            UserFlag::create([
                'type'          => 'visit',
                'value'         => 1,
                'user_id'       => $user->id,
                'flaggable_id'   => $catchphrase->id,
                'flaggable_type' => Catchphrase::class
            ]);
        });
    }
}
