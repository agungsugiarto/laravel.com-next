<?php

namespace App\Http\Controllers;

use App\LumenDocumentation;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class LumenDocsController extends Controller
{
    /**
     * The documentation repository.
     *
     * @var \App\LumenDocumentation
     */
    protected $docs;

    /**
     * Create a new controller instance.
     *
     * @param \App\LumenDocumentation $docs
     * @return void
     */
    public function __construct(LumenDocumentation $docs)
    {
        $this->docs = $docs;
    }

    /**
     * Show the root lumen documentation page (/lumen-docs).
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showRootPage()
    {
        return redirect('lumen-docs/'. DEFAULT_VERSION);
    }

    /**
     * Show a lumen documentation page.
     *
     * @param string      $version
     * @param string|null $page
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function show($version, $page = null)
    {
        if (! $this->isVersion($version)) {
            return redirect('lumen-docs' . DEFAULT_VERSION . '/' . $version, 301);
        }

        if (! defined('CURRENT_VERSION')) {
            define('CURRENT_VERSION', $version);
        }

        $sectionPage = $page ?: 'installation';
        $content = $this->docs->get($version, $sectionPage);

        if (is_null($content)) {
            $otherVersions = $this->docs->versionsContainingPage($page);

            return response()->view('lumen-docs', [
                'title' => 'Page not found',
                'index' => $this->docs->getIndex($version),
                'content' => view('docs-missing', [
                    'otherVersions' => $otherVersions,
                    'page' => $page,
                ]),
                'currentVersion' => $version,
                'versions' => LumenDocumentation::getDocVersions(),
                'currentSection' => $otherVersions->isEmpty() ? '' : '/' . $page,
                'canonical' => null,
            ], 404);
        }

        $title = (new Crawler($content))->filterXPath('//h1');

        $section = '';

        if ($this->docs->sectionExists($version, $page)) {
            $section .= '/' . $page;
        } elseif (! is_null($page)) {
            return redirect('/lumen-docs/' . $version);
        }

        $canonical = null;

        if ($this->docs->sectionExists(DEFAULT_VERSION, $sectionPage)) {
            $canonical = 'lumen-docs/' . DEFAULT_VERSION . '/' . $sectionPage;
        }

        return view('lumen-docs', [
            'title' => count($title) ? $title->text() : null,
            'index' => $this->docs->getIndex($version),
            'content' => $content,
            'currentVersion' => $version,
            'versions' => LumenDocumentation::getDocVersions(),
            'currentSection' => $section,
            'canonical' => $canonical,
        ]);
    }

    /**
     * Determine if the given URL segment is valid version.
     *
     * @param string $version
     * @return boolean
     */
    protected function isVersion($version)
    {
        return array_key_exists($version, LumenDocumentation::getDocVersions());
    }
}
