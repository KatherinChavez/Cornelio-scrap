<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //
        $company=session('company');
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        $this->mapWebAdminRoutes();
        $this->mapWebScrapRoutes();
        $this->mapWebDefaultRoutes();
        $this->mapWebCategoryRoutes();
        $this->mapWebSearchRoutes();
        $this->mapWebClassificationRoutes();
        $this->mapWebStatisticsRoutes();
        $this->mapWebReportRoutes();
        $this->mapWebAlertRoutes();
        $this->mapWebComparatorRoutes();
        $this->mapWebNotificationRoutes();
        $this->mapWebCronRoutes();
        $this->mapWebClassifyTopicsRoutes();
        $this->mapWebTelephoneRoutes();
        $this->mapWebTwitterRoutes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }

    protected function mapWebDefaultRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    protected function mapWebScrapRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Scrap.php'));
    }

    protected function mapWebCategoryRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Category.php'));
    }

    protected function mapWebSearchRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Search.php'));
    }

    protected function mapWebClassificationRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Classification.php'));
    }

    protected function mapWebAdminRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Admin.php'));
    }

    protected function mapWebStatisticsRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Statistics.php'));
    }

    protected function mapWebReportRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Report.php'));
    }

    protected function mapWebAlertRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Alert.php'));
    }

    protected function mapWebComparatorRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Comparator.php'));
    }

    protected function mapWebNotificationRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Notification.php'));
    }

    protected function mapWebCronRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Cron.php'));
    }

    protected function mapWebClassifyTopicsRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/ClassifyTopics.php'));
    }
    protected function mapWebTelephoneRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Telephone.php'));
    }
    protected function mapWebTwitterRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web/Twitter.php'));
    }

    
}
