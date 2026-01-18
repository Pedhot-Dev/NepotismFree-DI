<?php

declare(strict_types=1);

namespace PedhotDev\NepotismFree\Component\Compiler;

use ReflectionClass;
use ReflectionNamedType;
use PedhotDev\NepotismFree\Core\Registry;

/**
 * Generates a static PHP class for the container.
 */
class Compiler
{
    public function __construct(private Registry $registry) {}

    public function compile(string $path, string $className): void
    {
        $code = "<?php\n\ndeclare(strict_types=1);\n\n";
        $code .= "namespace PedhotDev\NepotismFree\Compiled;\n\n";
        $code .= "use PedhotDev\NepotismFree\Contract\ContainerInterface;\n";
        $code .= "use PedhotDev\NepotismFree\Exception\NotFoundException;\n\n";
        $code .= "class {$className} implements ContainerInterface\n{\n";
        $code .= "    private array \$instances = [];\n\n";
        
        $code .= "    public function get(string \$id, bool \$internal = false): mixed\n    {\n";
        $code .= "        if (isset(\$this->instances[\$id])) return \$this->instances[\$id];\n\n";
        $code .= "        return match (\$id) {\n";
        
        foreach ($this->registry->getServiceIds() as $id) {
            $binding = $this->registry->getBinding($id);
            if (is_string($binding)) {
                $code .= "            '{$id}' => \$this->instances['{$id}'] = new \\{$binding}(),\n";
            }
        }
        
        $code .= "            default => throw new NotFoundException(\"Service '\$id' not found in compiled container.\")\n";
        $code .= "        };\n";
        $code .= "    }\n\n";
        
        $code .= "    public function has(string \$id): bool\n    {\n";
        $code .= "        return false; // To be implemented\n";
        $code .= "    }\n\n";

        $code .= "    public function getTagged(string \$tag): iterable\n    {\n";
        $code .= "        return []; // To be implemented\n";
        $code .= "    }\n";

        $code .= "}\n";

        file_put_contents($path, $code);
    }
}
