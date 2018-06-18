<?php

namespace Caffeinated\Modules\Tests;

class BladeTest extends BaseTestCase
{
    protected $finder;

    protected $compiler;

    public function setUp()
    {
        $this->compiler = new \Illuminate\View\Compilers\BladeCompiler(
            \Mockery::mock('Illuminate\Filesystem\Filesystem'), __DIR__
        );

        $this->compiler->directive('module', function ($slug) {
            return "<?php if(Module::exists({$slug}) && Module::isEnabled({$slug})): ?>";
        });

        $this->compiler->directive('endmodule', function () {
            return '<?php endif; ?>';
        });
    }

    /** @test */
    public function it_can_compile_module_statement()
    {
        $this->assertSame('<?php if(Module::exists("blade") && Module::isEnabled("blade")): ?>', $this->compiler->compileString('@module("blade")'));
    }

    /** @test */
    public function it_can_compile_endmodule_statement()
    {
        $this->assertSame('<?php endif; ?>', $this->compiler->compileString('@endmodule("blade")'));
    }

    public function tearDown()
    {
        \Mockery::close();

        parent::tearDown();
    }
}
