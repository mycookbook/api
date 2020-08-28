<?php

namespace Functional\Models;

use App\Definition;

class DefinitionModelTest extends \TestCase
{
	/**
	 * @test
	 */
	public function it_is_an_instance_of_eloquent_model()
	{
		$definition = new Definition();
		$this->assertInstanceOf('Illuminate\Database\Eloquent\Model', $definition);
	}
}