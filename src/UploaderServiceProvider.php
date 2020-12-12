<?php
namespace Liucg1995\Uploader;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Liucg1995\Uploader\Adapter\OSS;
use Liucg1995\Uploader\Adapter\Upyun;
use Liucg1995\Uploader\Services\FileUpload;
use Liucg1995\Uploader\Adapter\Local;
use Liucg1995\Uploader\Adapter\Qiniu;
use Illuminate\Support\Facades\Route;

class UploaderServiceProvider extends ServiceProvider
{

    public function boot()
    {
        Uploader::register();

        View::share('uploader_options', Uploader::build());
    }

    public function register(){
        $this->loadRoute();
        $this->loadViews();
        $this->loadAssets();
        $this->registerDirective();

        $this->app->singleton(FileUpload::class, function ($app) {
            return new FileUpload($app['filesystem']);
        });

        $this->app->singleton(UploaderManager::class, function ($app){
            return new UploaderManager($app->request);
        });
    }

    protected function loadRoute(){
        if (! $this->app->routesAreCached()){
            Route::post('Liucg1995/upload', __NAMESPACE__.'\Http\Controllers\UploaderController@upload')->name('Liucg1995.upload');
        }
    }

    protected function loadViews()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'uploader');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/uploader'),
        ]);
    }

    protected function loadAssets()
    {
        $this->publishes([
            __DIR__.'/../resources/public' => public_path('vendor/uploader'),
        ], 'public');
    }

    protected function registerDirective(){
        Blade::directive('uploader', function($expression) {
            if (str_contains($expression, ',')){
                $parts = explode(',', trim($expression, '()'));
                $data = count($parts) > 1 ? implode(',', $parts) : '[]';
                return "<?php echo \$__env->make('uploader::uploader', (array)$data)->render(); ?>";
            }else{
                return "<?php echo \$__env->make('uploader::assets')->render(); ?>";
            }
        });
    }

}