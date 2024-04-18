<?php

namespace App\Libraries\Xtract;

class Payloads
{
    public static function UserByScreenName(string $name): array
    {
        return [
            'endpoint' => 'https://api.twitter.com/graphql/k5XapwcSikNsEsILW5FvgA/UserByScreenName?',
            'params' => http_build_query([
                'variables' => json_encode([
                    'screen_name' => $name,
                    'withSafetyModeUserFields' => true
                ]),
                'features' => json_encode([
                    "hidden_profile_likes_enabled" => true,
                    "hidden_profile_subscriptions_enabled" => true,
                    "responsive_web_graphql_exclude_directive_enabled" => true,
                    "verified_phone_label_enabled" => false,
                    "subscriptions_verification_info_is_identity_verified_enabled" => true,
                    "subscriptions_verification_info_verified_since_enabled" => true,
                    "highlights_tweets_tab_ui_enabled" => true,
                    "responsive_web_twitter_article_notes_tab_enabled" => true,
                    "creator_subscriptions_tweet_preview_api_enabled" => true,
                    "responsive_web_graphql_skip_user_profile_image_extensions_enabled" => false,
                    "responsive_web_graphql_timeline_navigation_enabled" => true
                ])
            ])
        ];
    }

    public static function TweetResultByRestId(string $id): array
    {
        return [
            'endpoint' => 'https://api.twitter.com/graphql/pq4JqttrkAz73WE6s2yUqg/TweetResultByRestId?',
            'params' => http_build_query([
                'variables' => json_encode([
                    "tweetId" => $id,
                    "withCommunity" => false,
                    "includePromotedContent" => false,
                    "withVoice" => false
                ]),
                'features' => json_encode([
                    "creator_subscriptions_tweet_preview_api_enabled" => true,
                    "c9s_tweet_anatomy_moderator_badge_enabled" => true,
                    "tweetypie_unmention_optimization_enabled" => true,
                    "responsive_web_edit_tweet_api_enabled" => true,
                    "graphql_is_translatable_rweb_tweet_is_translatable_enabled" => true,
                    "view_counts_everywhere_api_enabled" => true,
                    "longform_notetweets_consumption_enabled" => true,
                    "responsive_web_twitter_article_tweet_consumption_enabled" => true,
                    "tweet_awards_web_tipping_enabled" => false,
                    "freedom_of_speech_not_reach_fetch_enabled" => true,
                    "standardized_nudges_misinfo" => true,
                    "tweet_with_visibility_results_prefer_gql_limited_actions_policy_enabled" => true,
                    "rweb_video_timestamps_enabled" => true,
                    "longform_notetweets_rich_text_read_enabled" => true,
                    "longform_notetweets_inline_media_enabled" => true,
                    "responsive_web_graphql_exclude_directive_enabled" => true,
                    "verified_phone_label_enabled" => false,
                    "responsive_web_graphql_skip_user_profile_image_extensions_enabled" => false,
                    "responsive_web_graphql_timeline_navigation_enabled" => true,
                    "responsive_web_enhance_cards_enabled" => false
                ])
            ])
        ];
    }
}
