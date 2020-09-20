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

	/**
	 * creates users
	 * @param \Faker\Generator $faker
	 */
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
			'expertise_level' => 'professional bartender @ macys',
			'can_take_orders' => false,
			'about' => $faker->paragraphs(2),
			'email_verified' => '2020-01-01 00:00:00'
		]);

		$user->name_slug = slugify($user->name);

		$user->save();

		$this->user = $user;

		$this->createContactDetail($user);
		$this->createEmailVerification($user);
	}

	/**
	 * creates cookbooks
	 * @param \Faker\Generator $faker
	 */
	private function createCookbooks(\Faker\Generator $faker)
	{
		$cookbook = null;
		$cookbooks = [];

		for ($i= 0; $i<5; $i++) {
			$cookbook =  new \App\Cookbook([
				'name' => $faker->word,
				'description' => $faker->sentence(150),
				'bookCoverImg' => $faker->imageUrl(),
				'flag_id' => 35,
				'user_id' => $this->user->id,
				'resource_type' => 'cookbook',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			]);

			$cookbook->save();
			$cookbook->users()->attach($this->user->id);
			$cookbook->categories()->attach([3,6]);
			$cookbooks[] = $cookbook->id;
		}

		$this->cookbooks = $cookbooks;
	}

	/**
	 * Creates Recipes
	 *
	 * @param \Faker\Generator $faker
	 */
	private function createRecipes(\Faker\Generator $faker)
	{
		$recipe = null;
		$recipes = [];

		for ($k=0; $k<20; $k++) {
			$recipe = new \App\Recipe([
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
			$recipe->slug = slugify($recipe->name);

			$recipe->save();
			$this->createVariation($faker, $recipe);
			$recipes[] = $recipe->id;
		}

		$this->recipes = $recipes;
	}

	/**
	 * Creates Variation
	 *
	 * @param \Faker\Generator $faker
	 * @param \App\Recipe $recipe
	 */
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

	/**
	 * Add user contact detail
	 * @param \App\User $user
	 */
	private function createContactDetail(\App\User $user)
	{
		$contact = new \App\UserContactDetail([
			'user_id' => $user->id,
			'visibility' => 'public',
			'phone' => '(647) 000 0000',
			'facebook' => 'https://facebook.com/test-user',
			'twitter' => 'https://twitter.com/test-user',
			'instagram' => 'https://instagram.com/test-user',
			'skype' => '@test-user',
			'calendly' => 'https://calendly.com/test-user',
			'office_address' => '560 Tim Hortons Blvd ON M5V 3M3',
			'website' => 'www.mywebsite.com'
		]);
		$contact->save();
	}

	/**
	 * Create Verified user
	 * @param \App\User $user
	 */
	private function createEmailVerification(\App\User $user)
	{
		$verification = new \App\EmailVerification([
			'user_id' => $user->id,
			'token' => \Illuminate\Support\Facades\Crypt::encrypt(['user_id' => $user->id, 'email' => $user->email, 'secret' => env('CRYPT_SECRET')]),
			'is_verified' => \Carbon\Carbon::now()
		]);
		$verification->save();
	}
}
