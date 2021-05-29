<?php

declare(strict_types = 1);

namespace Tests;

use Tester\Assert;
use Nette\Application\Request;
use Tests\TestCase\WebTestCase;
use Nette\Application\Responses\TextResponse;


require_once __DIR__ . '/bootstrap.php';


final class ExamplePresenterTest extends WebTestCase
{

	/** @dataProvider provideActions */
	public function testAction(?string $action): void
	{
		$response = $this->request(new Request('Example', 'GET', [
			'action' => $action,
		]));

		Assert::type(TextResponse::class, $response);
	}


	/** @return array<int, array<int, string|null>> */
	public function provideActions(): array
	{
		return [
			[null],
			['homepage'],
			['sorting'],
			['filtering'],
			['rowActions'],
			['groupActions'],
			['inline'],
			['pagination'],
			['full'],
		];
	}

}

(new ExamplePresenterTest)->run();
