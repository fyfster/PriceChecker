<?php
namespace App\Http\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class Http
{
    CONST METHOD_POST = "POST";
    CONST METHOD_GET = "GET";
    CONST RESPONSE_TYPE_SUCCESS = 1;
    CONST RESPONSE_TYPE_ERROR = 0;
    CONST LOCAL_ENVIRONMENT = 'local';

    private $menuItems;

    /** Generates a HTTP response
     *
     * @param bool $response - depending on response it is error or success json
     * @param string $code - the message code
     * @param array $data - information to send
     * @param int $responseCode - HTTP response
     * @return \Illuminate\Http\Response
     */
    public function generateJsonResponse($response, $code = null, $data = array(), $responseCode =  Response::HTTP_OK)
    {
        $responseData = array('success' => $response);

        if ($response === Http::RESPONSE_TYPE_ERROR) {
            $responseData['errorCode'] = $code;
            $responseData['errorMessage'] = trans('codes.' . $code);
        }

        if ($response === Http::RESPONSE_TYPE_SUCCESS && !empty($code)) {
            $responseData['successMessage'] = trans('codes.' . $code);
        }

        if (!empty($data) || $response === Http::RESPONSE_TYPE_SUCCESS) {
            $responseData['data'] = $this->removeNullFromData($data);
        }

        if (env('APP_ENV') == self::LOCAL_ENVIRONMENT) {
            Log::info(json_encode($responseData));
        }

        return response($responseData, $responseCode);
    }

    /** Generates a HTTP response
     *
     * @param string $draw - The draw counter that this object is a response to - from the draw parameter sent as part of the data request
     * @param string $recordsTotal - Total records, before filtering (i.e. the total number of records in the database)
     * @param string $recordsFiltered - Total records, after filtering (i.e. the total number of records after filtering has been applied - not just the number of records being returned for this page of data).
     * @param array $data - The data to be displayed in the table
     * @return \Illuminate\Http\Response
     */
    public function generateDataTableResponse($draw, $recordsTotal, $recordsFiltered, $data)
    {
        $responseData = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        );

        if (env('APP_ENV') == self::LOCAL_ENVIRONMENT) {
            Log::info(json_encode($responseData));
        }
        return response()->json($responseData);
    }

    /** Generates a HTTP response used in select2 dropdowns
     *
     * @param array $data - The data to be displayed in the dropdown
     * @param string $dataCount - Number of items
     * @return \Illuminate\Http\Response
     */
    public function generateSelect2Response($data, $dataCount)
    {
        $responseData = array(
            'items' => $data,
            'total_count' => $dataCount
        );

        if (env('APP_ENV') == self::LOCAL_ENVIRONMENT) {
            Log::info(json_encode($responseData));
        }
        return response()->json($responseData);
    }

    /**
     * Loads the view
     *
     * @param array $sessionInfo -information from session
     * @param array $data - information to send to page
     * @param string $page - page you want to display
     * @return \Illuminate\Contracts\View\Factory
     */
    public function displayView($sessionInfo = array(), $data = array(), $page = '/login')
    {
        if (!empty($sessionInfo) && env('APP_ENV') == self::LOCAL_ENVIRONMENT) {
            Log::info(json_encode(array_merge($data, $sessionInfo)));
        }
        $sessionInfo['is_logged_in'] = empty($sessionInfo)? 0 : 1;
        $sessionInfo['menu_items'] = $this->menuItems;
        $sessionInfo['base_url'] = URL::to('/');
        return view($page, array_merge($data, $sessionInfo));
    }

    /**
     * Loads an image
     *
     * @param array $file -information from session
     * @param array $type - information to send to page
     * @return file
     */
    public function displayImage($file, $type)
    {
        $response = \Illuminate\Support\Facades\Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    public function setMenuItems($menuItems)
    {
        $this->menuItems = $menuItems;
    }

    public function addSelectedToItem($itemUrl)
    {
        foreach ($this->menuItems as &$item) {
            if (isset($item['subItems'])) {
                $isSelected = false;
                foreach ($item['subItems'] as &$subItem) {
                    if (isset($subItem['url']) && $subItem['url'] == $itemUrl) {
                        $subItem['class'] = ' active open';
                        $isSelected = true;
                    }
                    if (isset($subItem['subItems'])) {
                        $isSubSelected = false;
                        foreach ($subItem['subItems'] as &$subSubItem) {
                            if (isset($subSubItem['url']) && $subSubItem['url'] == $itemUrl) {
                                $subSubItem['class'] = ' active open';
                                $isSubSelected = true;
                            }
                        }
                        if (true == $isSubSelected) {
                            $subItem['class'] = ' active open';
                            $isSelected = true;
                        }
                    }
                }
                if (true == $isSelected) {
                    $item['class'] = ' active open';
                }
            }
            if (isset($item['url']) && $item['url'] == $itemUrl) {
                $item['class'] = ' active open';
            }
        }
    }

    /**
     * Parses the data array and converts null values into ""
     *
     * @param array $data - array to convert
     * @return array
     */
    private function removeNullFromData($data)
    {
        foreach ($data as $key => $value) {
            if (is_null($value)) {
                $data[$key] = "";
            }
            if (is_array($value)) {
                $data[$key] = $this->removeNullFromData($data[$key]);
            }
        }
        return $data;
    }
}
