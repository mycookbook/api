<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
	protected $user;
	protected $cookbooks;
	protected $recipes;

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function run()
    {
    	//Defaults
        $this->call(DefinitionsSeeder::class);
        $this->call(FlagsSeeder::class);
        $this->call(CategoriesSeeder::class);

        //fakes
		$faker = Faker::create();

		$this->createUser($faker);
		$this->createCookbooks($faker);
		$this->createRecipes($faker);
    }

	private function createUser(\Faker\Generator $faker)
	{
		$user = new \App\User([
			'name' => $faker->firstName . ' ' . $faker->lastName,
			'email' => $faker->email,
			'password' =>  app('hash')->make('secret'),
			'followers' => 0,
			'following' => 0,
			'avatar' => $faker->imageUrl(),
			'pronouns' => 'She/Her',
			'expertise_level' => 'professional bartender @ macys'
		]);
		$user->name_slug = slugify($user->name);

		$user->save();

		$this->user = $user;
	}

	private function createCookbooks(\Faker\Generator $faker)
	{
		$cookbook1 = new \App\Cookbook([
			'name' => $faker->word,
			'description' => $faker->sentence(150),
			'bookCoverImg' => $faker->imageUrl(),
			'flag_id' => 35,
			'user_id' => $this->user->id,
			'resource_type' => 'cookbook',
			'created_at' => new DateTime(),
			'updated_at' => new DateTime()
		]);

		$cookbook1->save();
		$cookbook1->users()->attach($this->user->id);
		$cookbook1->categories()->attach([3,6]);

		$cookbook2 = new \App\Cookbook([
			'name' => $faker->word,
			'description' => $faker->sentence(150),
			'bookCoverImg' => $faker->imageUrl(),
			'flag_id' => 25,
			'user_id' => $this->user->id,
			'resource_type' => 'cookbook',
			'created_at' => new DateTime(),
			'updated_at' => new DateTime()
		]);

		$cookbook2->save();
		$cookbook2->users()->attach($this->user->id);
		$cookbook2->categories()->attach([4,5]);

		$this->cookbooks = [$cookbook1->id, $cookbook2->id];
	}

	private function createRecipes(\Faker\Generator $faker)
	{
		$recipe1 = new \App\Recipe([
			'name' => $faker->word,
			'ingredients' => json_encode(['data' => ['2 lbs red potatoes', '4 tablespoons', '1 medium onion chopped']]),
			'imgUrl' => $faker->imageUrl(),
			'description' => $this->stepByStepWithGifTemplate(),
			'cookbook_id' => $this->cookbooks[0],
			'summary' => $faker->sentence(20),
			'calorie_count' => 0,
			'cook_time' => '2020-07-09 01:45:00',
			'prep_time' => '2020-07-09 00:10:00',
			'nutritional_detail' => json_encode(['cal' => '462', 'carbs' => '42', 'protein' => '43', 'fat' => '28']),
			'servings' => 1,
			'user_id' => $this->user->id,
			'resource_type' => 'recipe'
		]);
		$recipe1->slug = slugify($recipe1->name);

		$recipe1->save();
		$this->createVariation($faker, $recipe1);

		$recipe2 = new \App\Recipe([
			'name' => $faker->word,
			'ingredients' => json_encode(['data' => ['2 lbs red potatoes', '4 tablespoons', '1 medium onion chopped']]),
			'imgUrl' => $faker->imageUrl(),
			'description' => $faker->paragraph(1),
			'cookbook_id' => $this->cookbooks[1],
			'summary' => $faker->sentence(20),
			'calorie_count' => 0,
			'cook_time' => '2020-07-09 01:15:00',
			'prep_time' => '2020-07-09 00:10:00',
			'nutritional_detail' => json_encode(['cal' => '462', 'carbs' => '42', 'protein' => '43', 'fat' => '28']),
			'servings' => 1,
			'user_id' => $this->user->id,
			'resource_type' => 'recipe'
		]);

		$recipe2->slug = slugify($recipe2->name);
		$recipe2->save();

		$this->recipes = [$recipe1->id, $recipe2->id];
	}

	private function createVariation(\Faker\Generator $faker, \App\Recipe $recipe)
	{
		$variation = new \App\RecipeVariation([
			'name' => $faker->word,
			'ingredients' => json_encode(['data' => ['2 lbs greek yoghurt']]),
			'imgUrl' => $faker->imageUrl(),
			'description' => $this->stepByStepWithGifTemplate(),
			'recipe_id' => $recipe->id,
			'resource_type' => 'recipe_variation'
		]);

		$variation->save();
	}

	private function stepByStepWithGifTemplate()
	{
		return '
		<h3>Step 1:</h3>
		<p>Id enim voluptatem quo et voluptates est sit aut error repudiandae exercitationem nostrum eius itaque iusto illo 
		ratione velit alias nobis eligendi eveniet adipisci repudiandae qui temporibus amet dignissimos consectetur voluptatem 
		consequatur sit unde beatae omnis qui sit minus quidem quae aut sunt incidunt optio voluptate consequatur libero qui 
		modi tempore qui accusamus similique magni non quasi natus maiores et expedita quod delectus in repellat quibusdam 
		rerum excepturi voluptatum accusantium suscipit eveniet accusamus sit ut et omnis ex incidunt velit voluptas consectetur 
		rem eos impedit vel provident voluptatem doloribus molestiae voluptatem facere delectus est ipsam hic placeat dignissimos 
		vel inventore velit tempore et non sit amet repellendus corrupti enim dignissimos minima odio quaerat unde illo voluptatem 
		corrupti ut fugit ut est id accusantium suscipit dolore voluptatem et cumque qui est voluptatum quia porro accusantium 
		reiciendis unde pariatur est voluptatem similique accusantium natus corporis voluptatibus deleniti magnam non porro 
		quibusdam repellendus quae ut cumque ipsum et nostrum ipsum nihil unde aperiam eos sit debitis mollitia possimus quia 
		quis suscipit sit soluta unde atque qui quis non nisi iusto atque.</p>
		
		<h3>Step 2:</h3>
		<p>Id enim voluptatem quo et voluptates est sit aut error repudiandae exercitationem nostrum eius itaque iusto illo 
		ratione velit alias nobis eligendi eveniet adipisci repudiandae qui temporibus amet dignissimos consectetur voluptatem 
		consequatur sit unde beatae omnis qui sit minus quidem quae aut sunt incidunt optio voluptate consequatur libero qui 
		modi tempore qui accusamus similique magni non quasi natus maiores et expedita quod delectus in repellat quibusdam 
		rerum excepturi voluptatum accusantium suscipit eveniet accusamus sit ut et omnis ex incidunt velit voluptas consectetur 
		rem eos impedit vel provident voluptatem doloribus molestiae voluptatem facere delectus est ipsam hic placeat dignissimos 
		vel inventore velit tempore et non sit amet repellendus corrupti enim dignissimos minima odio quaerat unde illo voluptatem 
		corrupti ut fugit ut est id accusantium suscipit dolore voluptatem et cumque qui est voluptatum quia porro accusantium 
		reiciendis unde pariatur est voluptatem similique accusantium natus corporis voluptatibus deleniti magnam non porro 
		quibusdam repellendus quae ut cumque ipsum et nostrum ipsum nihil unde aperiam eos sit debitis mollitia possimus quia 
		quis suscipit sit soluta unde atque qui quis non nisi iusto atque.</p>
		<br />
		<div>Hope you enoyed what you made!</div>
		<div class="ui grid">
			<div class="ui sixteen wide column">
				<img src="https://media.giphy.com/media/xT0xePlbYZgrRWHodi/giphy.gif" />
			</div>
		</div>';
	}
}
