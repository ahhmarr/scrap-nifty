<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Http\Request;

use App\Ticker;

class ScrappingController extends Controller
{
    public function index(Request $request)
    {
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
        $data = [];
        $stock = $dom->filter('#indian .Dtcon')
                ->each(function ($node, $index) {
                    return $this->stock($node);
                });
        $commodity = $dom->filter('#commodityindices tr')
                ->each(function ($node, $index) {
                    return $this->getRow($node);
                });
                
        $forex = $dom->filter('#forexindices tr')
        ->each(function ($node, $index) {
            return $this->getRow($node);
        });

        $data = array_merge($data, $commodity, $stock, $forex);
        $this->save($data);
        return [
            'success' => true,
            'message' => 'data crawled successfully'
        ];
    }
    
    
    private function stock($node)
    {
        $value = $node->filter('.mval')->text();
        $name = $node->filter('.fl')->eq(0)->text();
        $name = str_replace($value, '', $name);
        $mup = $node->filter('.fl')->eq(1)->text();
        
        return [$name,$value,$mup];
    }

    private function getRow($node)
    {
        $name = $node->filter('td')->eq(0)->text();
        $value = $node->filter('td')->eq(1)->text();
        $mup = $node->filter('td')->eq(3)->text();

        $resp = [$name,$value,$mup];
        return $resp;
    }
    
    private function clean($string)
    {
        $string = trim($string);
        $string = preg_replace("/(\n|\t)/", '', $string);
        $string = preg_replace("/ {2,}/", ' ', $string);
        return $string;
    }
    private function save($data)
    {
        foreach ($data as $value) {
            list($name, $value, $mup) = $value;
            
            $name = $this->clean($name);
            $value = $this->clean($value);
            $mup = $this->clean($mup);

            $obj = Ticker::whereName($name)->first() ?? new Ticker;
            
            $obj->name = $name;
            $obj->value = $value;
            $obj->mup = $mup;
            
            $obj->save();
        }
    }
}
