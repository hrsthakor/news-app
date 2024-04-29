<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    private $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://newsapi.org/v2';
    }

    public function index(Request $request)
    {
        try {
            $query = [
                'apiKey' => config('services.newsapi.key'),
                'q' => $request->input('search'),
                'sources' => $request->input('source'),
                'sortBy' => $request->input('sort_by', 'publishedAt'),
                'pageSize' => 10,
                'page' => $request->input('page', 1),
            ];

            if ($request->has('source')) {
                $query['sources'] = $request->input('source');
            }

            $response = Http::get("{$this->apiUrl}/everything", $query);
            if ($response->failed()) {
                throw new \Exception('Failed to fetch articles. Please try again later.');
            }

            $articles = $response->json()['articles'] ?? [];

            return view('home', compact('articles'));
            return view('home');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error fetching articles: ' . $e->getMessage());

            // Display a friendly error message to the user
            return back()->with('error', 'Failed to fetch articles. Please try again later.');
        }
    }
}
