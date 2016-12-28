<?php

namespace Caffeinated\Modules\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;

class BladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->afterResolving('blade.compiler', function (BladeCompiler $bladeCompiler) {

            // @module($slug)
            $bladeCompiler->directive('module', function ($slug) {
                return "<?php if(Module::exists({$slug}) && Module::isEnabled({$slug})): ?>";
            });
            $bladeCompiler->directive('endmodule', function () {
                return '<?php endif; ?>';
            });

        });
    }
}
