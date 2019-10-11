<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

use App\Ticker;

class ScrappingController extends Controller
{
    public function index(Request $request) {
        return Ticker::all();
    }
    public function init(Request $request)
    {
        $url = 'https://www.way2wealth.com/market/overview/';
        // $url = 'http://scrap-nifty.test/dump.html';
        $client = new Client();
        $request = $client->get($url)
                            ->getBody()
                            ->getContents();
        $dom = new Crawler($request);
        $table = $dom->filter('#indian .Dtcon')
                    ->each(function ($node, $index) {
                        $val = $node->filter('.mval')->text();
                        $ticker = $node->filter('.fl')->eq(0)->text();
                        $ticker = str_replace($val, '', $ticker);
                        $mup = $node->filter('.fl')->eq(1)->text();
                        $obj = Ticker::whereName($ticker)->first() ?? new Ticker;
                        $obj->name = $ticker;
                        $obj->value = $val;
                        $obj->mup = $mup;
                        $obj->save();
                    });
        return [
            'success' => true,
            'message' => 'data crawled successfully'
        ];
    }
}
