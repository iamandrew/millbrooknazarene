<?php
/* @var Concrete\Core\Application\Application $app */
/* @var Concrete\Core\Console\Application $console only set in CLI environment */

/*
 * ----------------------------------------------------------------------------
 * # Custom Application Handler
 *
 * You can do a lot of things in this file.
 *
 * ## Set a theme by route:
 *
 * $app->make('\Concrete\Core\Page\Theme\ThemeRouteCollection')
 * ->setThemeByRoute('/login', 'greek_yogurt');
 *
 *
 * ## Register a class override.
 *
 * $app->bind('helper/feed', function() {
 * 	 return new \Application\Core\CustomFeedHelper();
 * });
 *
 * $app->bind('\Concrete\Attribute\Boolean\Controller', function($app, $params) {
 * 	return new \Application\Attribute\Boolean\Controller($params[0]);
 * });
 *
 * ## Register Events.
 *
 * Events::addListener('on_page_view', function($event) {
 * 	$page = $event->getPageObject();
 * });
 *
 *
 * ## Register some custom MVC Routes
 *
 * Route::register('/test', function() {
 * 	print 'This is a contrived example.';
 * });
 *
 * Route::register('/custom/view', '\My\Custom\Controller::view');
 * Route::register('/custom/add', '\My\Custom\Controller::add');
 *
 * ## Pass some route parameters
 *
 * Route::register('/test/{foo}/{bar}', function($foo, $bar) {
 *  print 'Here is foo: ' . $foo . ' and bar: ' . $bar;
 * });
 *
 *
 * ## Override an Asset
 *
 * use \Concrete\Core\Asset\AssetList;
 * AssetList::getInstance()
 *     ->getAsset('javascript', 'jquery')
 *     ->setAssetURL('/path/to/new/jquery.js');
 *
 * or, override an asset by providing a newer version.
 *
 * use \Concrete\Core\Asset\AssetList;
 * use \Concrete\Core\Asset\Asset;
 * $al = AssetList::getInstance();
 * $al->register(
 *   'javascript', 'jquery', 'path/to/new/jquery.js',
 *   array('version' => '2.0', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => false, 'combine' => false)
 *   );
 *
 * ----------------------------------------------------------------------------
 */

require_once DIR_APPLICATION . '/src/KidsClub/RegistrationSheet.php';

\Route::register(
    '/kids-club-2026/register',
    static function () {
        $request = \Request::getInstance();

        $value = static function (string $key) use ($request): string {
            return trim((string) $request->request->get($key, ''));
        };

        $json = static function (array $payload, int $status = 200): \Symfony\Component\HttpFoundation\JsonResponse {
            return new \Symfony\Component\HttpFoundation\JsonResponse($payload, $status);
        };

        if ($value('website') !== '') {
            return $json([
                'ok' => true,
                'message' => 'Thank you. The registration has been received.',
            ]);
        }

        $required = [
            'guardian_name',
            'guardian_email',
            'guardian_phone',
            'home_address',
            'child_name',
            'date_of_birth',
            'emergency_contact_name',
            'emergency_contact_relationship',
            'emergency_contact_phone',
            'medication',
            'allergies',
            'additional_needs',
            'photo_video_permission',
            'first_aid_consent',
            'privacy_acknowledgement',
        ];

        foreach ($required as $field) {
            if ($value($field) === '') {
                return $json([
                    'ok' => false,
                    'message' => 'Please complete all required details before sending.',
                ], 422);
            }
        }

        if (!filter_var($value('guardian_email'), FILTER_VALIDATE_EMAIL)) {
            return $json([
                'ok' => false,
                'message' => 'Please check the email address before sending.',
            ], 422);
        }

        $spreadsheetId = getenv('MILLBROOK_KIDS_CLUB_2026_SPREADSHEET_ID') ?: '1DzIYGJEPE_w_-Eh1fIMtHjCu9EiqMNmPlvlQOQrofcw';
        $range = getenv('MILLBROOK_KIDS_CLUB_2026_SHEET_RANGE') ?: 'Sheet1!A1:Z';
        $credentialsPath = getenv('MILLBROOK_GOOGLE_SERVICE_ACCOUNT_PATH') ?: null;

        $row = [
            date('c'),
            $value('event_name') ?: 'The Big Picnic',
            $value('event_year') ?: '2026',
            $value('event_dates') ?: '12-14 August 2026',
            $value('guardian_name'),
            $value('guardian_email'),
            $value('guardian_phone'),
            $value('home_address'),
            $value('child_name'),
            $value('date_of_birth'),
            $value('child_age_on_first_day'),
            $value('emergency_contact_name'),
            $value('emergency_contact_relationship'),
            $value('emergency_contact_phone'),
            $value('medication'),
            $value('allergies'),
            $value('additional_needs'),
            $value('photo_video_permission'),
            $value('first_aid_consent'),
            $value('privacy_acknowledgement'),
            $value('future_contact_permission') ?: 'No',
            $value('source_page') ?: '/kids-club-2026',
        ];

        try {
            $sheet = new \Application\KidsClub\RegistrationSheet($spreadsheetId, $range, $credentialsPath);
            $sheet->append($row);
        } catch (\RuntimeException $exception) {
            error_log('[kids-club-2026] ' . $exception->getMessage());

            if (strpos($exception->getMessage(), 'credentials') !== false) {
                return $json([
                    'ok' => false,
                    'message' => 'The registration form is ready, but the Google Sheet connection is not configured yet. Please email info@millbrooknazarene.co.uk.',
                ], 503);
            }

            return $json([
                'ok' => false,
                'message' => 'Sorry, the registration could not be sent. Please try again or email info@millbrooknazarene.co.uk.',
            ], 500);
        }

        return $json([
            'ok' => true,
            'message' => 'Thank you. The registration has been sent to the Kids Club team.',
        ]);
    },
    'kids_club_2026_register',
    [],
    [],
    '',
    [],
    ['POST']
);

\Route::register(
    '/givealittle/start',
    static function () {
        $request = \Request::getInstance();

        $campaignId = 'd7cf8912-aaae-4fb2-8c5b-224f5b3ac8a3';
        $campaignUrl = 'https://givealittle.co/c/millbrook-nazarene-giving';
        $donationActionUrl = 'https://givealittle.co/c/' . $campaignId . '/select-amount';

        $redirect = static function (string $url): \Symfony\Component\HttpFoundation\RedirectResponse {
            return new \Symfony\Component\HttpFoundation\RedirectResponse($url, 303);
        };

        $amountValue = trim((string) $request->request->get('amount', ''));
        $amountValue = str_replace(',', '.', $amountValue);
        $amount = is_numeric($amountValue) ? (float) $amountValue : 0.0;

        if ($amount < 1 || $amount > 1000) {
            return $redirect($campaignUrl);
        }

        $isRecurringValue = strtolower(trim((string) $request->request->get('isRecurring', 'false')));
        $isRecurring = in_array($isRecurringValue, ['1', 'true', 'monthly'], true) ? 'true' : 'false';
        $tag = preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $request->request->get('tag', 'millbrook-web'));
        $tag = substr($tag ?: 'millbrook-web', 0, 36);

        $postFields = http_build_query([
            'amount' => rtrim(rtrim(number_format($amount, 2, '.', ''), '0'), '.'),
            'isRecurring' => $isRecurring,
            'tag' => $tag,
        ]);

        $ch = curl_init($donationActionUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Origin: https://givealittle.co',
                'Referer: ' . $campaignUrl,
                'User-Agent: MillbrookChurchWebsite/1.0',
            ],
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response !== false && $status >= 300 && $status < 400) {
            $headers = substr($response, 0, $headerSize);

            if (preg_match('/^location:\s*(.+)$/mi', $headers, $matches)) {
                $location = trim($matches[1]);

                if (strpos($location, 'https://givealittle.co/c/') === 0) {
                    return $redirect($location);
                }
            }
        }

        error_log('[givealittle] Unable to start donation handoff. HTTP ' . $status . ($error ? ' - ' . $error : ''));

        return $redirect($campaignUrl . '#' . rawurlencode($tag));
    },
    'givealittle_start',
    [],
    [],
    '',
    [],
    ['POST']
);
