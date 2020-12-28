<?php

use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
	protected $users;
	protected $cookbooks;
	protected $recipes;
	protected $images = [
		'https://i.pinimg.com/originals/0d/91/1b/0d911b9d554b317d6e19aa4c9b55c0a0.jpg',
		'https://i.pinimg.com/originals/f0/b6/15/f0b615f78dd809d68ec389f4bc8d94bb.jpg',
		'https://image.winudf.com/v2/image/Y29tLnl0b2ZmbGluZWJpcnlhbmlfc2NyZWVuXzdfMTUxNTg4NjgxN18wNzE/screen-7.jpg?fakeurl=1&type=.jpg',
		'https://i.pinimg.com/originals/96/e9/c1/96e9c13abb804bef082d218a36cc1d37.jpg',
		'https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&w=1000&q=80',
		'https://i.pinimg.com/originals/59/5a/00/595a00a1954d27d774457746c3a7ebcd.jpg',
		'https://eskipaper.com/images/awesome-seafood-wallpaper-1.jpg',
		'https://i.pinimg.com/originals/59/ac/29/59ac29392d87d20728724dab4eef3eec.jpg',
		'https://c4.wallpaperflare.com/wallpaper/208/568/982/food-mexican-corn-wallpaper-preview.jpg',
		'https://cancunmexicanbarandgrill.com/files/2019/03/dl2.jpg'
	];

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 * @throws Exception
	 */
    public function run()
    {
		DB::transaction(function () {
			//Defaults
			$this->call(DefinitionsSeeder::class);
			$this->call(FlagsSeeder::class);
			$this->call(CategoriesSeeder::class);
			$this->call(StaticContentsSeeder::class);

			if (env('APP_ENV') !== 'local') {
				$admin = new \App\User([
					'name' => 'Florence Okosun',
					'email' => 'okosunuzflorence@gmail.com',
					'password' =>  app('hash')->make('0B10r@.UM3h'),
					'followers' => 0,
					'following' => 0,
					'avatar' => '',
					'pronouns' => 'She/Her',
					'expertise_level' => 'Founder',
					'can_take_orders' => false,
					'about' => '',
					'email_verified' => '2020-01-01 00:00:00',
					'name_slug' => 'florence-okosun'
				]);
				$admin->save();
				$this->createContactDetail($admin);

				$editor = new \App\User([
					'name' => 'Tony Udomaye',
					'email' => 'udomiayetony@gmail.com',
					'password' =>  app('hash')->make('secret'),
					'followers' => 0,
					'following' => 0,
					'avatar' => 'https://ca.slack-edge.com/T5QPN806A-U01A3835GPP-72238718978f-512',
					'pronouns' => 'He/Him',
					'expertise_level' => 'VP Product Engineering',
					'can_take_orders' => false,
					'about' => '',
					'email_verified' => '2020-01-01 00:00:00',
					'name_slug' => 'tony-udomaiye'
				]);
				$editor->save();
				$this->createContactDetail($editor);

				$contributor = new \App\User([
					'name' => 'Test user',
					'email' => 'test@somemail.com',
					'password' =>  app('hash')->make('secret'),
					'followers' => 0,
					'following' => 0,
					'avatar' => '',
					'pronouns' => 'They/Them',
					'expertise_level' => 'Freelancer',
					'can_take_orders' => true,
					'about' => '',
					'email_verified' => null,
					'name_slug' => 'test-user'
				]);

				$contributor->save();
				$this->createContactDetail($contributor);

				//create cookbooks for canada, us and african countries. dont add recipes to any
				$cookbook =  new \App\Cookbook([
					'name' => 'Nigerian Party Food (Owambe)',
					'description' => 'A collection of common Nigerian Party foods, everything from Jollof rice to swallows and soups to mention a few. This cookbook may contain contributions from multiple contributors and content thereof belongs to cookbookhq. Dive right in to browse different Nigerian party food recipes.',
					'bookCoverImg' => 'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/nigeria-party-food.jpg',
					'flag_id' => 1,
					'user_id' => $admin->id,
					'resource_type' => 'cookbook',
					'created_at' => new DateTime(),
					'updated_at' => new DateTime()
				]);

				$cookbook->save();

				//create one authorized client

				$api_key = Str::random(50);
				$passphrase = Str::random(10);
				$secret = Illuminate\Support\Facades\Crypt::encrypt($api_key . "." . $passphrase);

				$authorized_client = new \App\AuthorizedClient([
					'api_key' => $api_key,
					'client_secret' => $secret,
					'passphrase' => $passphrase
				]);

				$authorized_client->save();

			} else {
				//fakes
				$faker = Faker::create();

				$this->createUsers($faker);
				$this->createCookbooks($faker);
				$this->createRecipes($faker);
			}
		});
    }

	/**
	 * creates users
	 * @param \Faker\Generator $faker
	 */
	private function createUsers(\Faker\Generator $faker)
	{
		$avatars = [
			'https://bit.ly/35RTEfA',
			'https://bit.ly/2RIHDB9',
			'https://bit.ly/2FKXI6Q',
			'https://bit.ly/32NIPtl',
			'https://bit.ly/3cgr09f',
			'https://bit.ly/35RTEfA',
			'https://bit.ly/2RIHDB9',
			'https://bit.ly/2FKXI6Q',
			'https://bit.ly/32NIPtl',
			'https://bit.ly/3cgr09f',
			'https://bit.ly/35RTEfA',
			'https://bit.ly/2RIHDB9',
			'https://bit.ly/2FKXI6Q',
			'https://bit.ly/32NIPtl',
			'https://bit.ly/3cgr09f',
			'https://bit.ly/35RTEfA',
			'https://bit.ly/2RIHDB9',
			'https://bit.ly/2FKXI6Q',
			'https://bit.ly/32NIPtl',
			'https://bit.ly/3cgr09f'
		];

		$user = null;
		$user_ids = [];
		$pronouns = ['She/Her', 'He/Him', 'They/Them'];
		$can_take_orders = [true, false];
		$levels = [
			'Dad',
			'Working Mum',
			'Chef',
			'Hobbyist',
			'professional bartender @ macys',
			'farmer',
			'Just a Girl',
			'Cook',
			'Care giver'
		];

		for ($j=0; $j<20; $j++) {
			$rand_level = array_rand($levels);

			$user = new \App\User([
				'name' => $faker->firstName . ' ' . $faker->lastName,
				'email' => $faker->email,
				'password' =>  app('hash')->make('secret'),
				'followers' => 0,
				'following' => 0,
				'avatar' => $avatars[$j],
				'pronouns' => $pronouns[rand(0,2)],
				'expertise_level' => $levels[$rand_level],
				'can_take_orders' => array_rand($can_take_orders),
				'about' => $faker->paragraph(6),
				'email_verified' => '2020-01-01 00:00:00'
			]);

			$user->name_slug = slugify($user->name);

			$user->save();

			$this->user = $user;

			$this->createContactDetail($user);
			$this->createEmailVerification($user);
			$user_ids[] = $user->id;
		}

		$this->users = $user_ids;
	}

	/**
	 * creates cookbooks
	 * @param \Faker\Generator $faker
	 */
	private function createCookbooks(\Faker\Generator $faker)
	{
		$cookbook = null;
		$cookbooks = [];
		$lucky_spin = array_rand($this->users);
		$random_author_id = $this->users[$lucky_spin];

		$sample_titles = [
			'Vegan Cookbook',
			'A beginners Guide to Modern Cookery',
			'Bread and Muffins',
			'Homemade Cocktails',
			'Be your own Chef',
			'The 5 minute Chef',
			'What to do with leftovers',
			'Meat Lovers',
			'For my furry friends',
			'European Food'
		];

		for ($i= 0; $i<10; $i++) {
			$flag_ids = range(2, 35);
			$lucky_flagId_spin = array_rand($flag_ids);
			$flagId = $flag_ids[$lucky_flagId_spin];

			$cookbook =  new \App\Cookbook([
				'name' => $sample_titles[$i],
				'description' => $faker->sentence(150),
				'bookCoverImg' => $this->images[$i],
				'flag_id' => $flagId,
				'user_id' => $random_author_id,
				'resource_type' => 'cookbook',
				'created_at' => new DateTime(),
				'updated_at' => new DateTime()
			]);

			$cookbook->save();
//			$random_users = array_rand($this->users, rand(2, 5));
			$cookbook->users()->attach($cookbook->user_id);

//			foreach ($random_users as $key => $val) {
//				$cookbook->users()->attach($val);
//			}

			$category_ids = range(1, 6);
			$random_category_ids = array_rand($category_ids, rand(2, 3));

			foreach($random_category_ids as $key => $val) {
				if ($val == 0) {
					$cookbook->users()->attach(2);
				} else {
					$cookbook->categories()->attach($val);
				}
			}

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
		$cuisine = [
			'chinese',
			'mexican',
			'african',
			'french',
			'italian',
			'thai',
			'spanish',
			'indian'
		];

		$course = [
			'main',
			'side',
			'dessert'
		];

		$recipe = null;
		$recipes = [];

		for ($k=0; $k<10; $k++) {
			$random_cookbookId = array_rand($this->cookbooks);
			$random_userId = array_rand($this->users);
			$random_cuisine = array_rand($cuisine);
			$random_course = array_rand($course);

			$recipe = new \App\Recipe([
				'name' => $faker->word,
				'ingredients' => json_encode(['data' => ['2 lbs red potatoes', '4 tablespoons', '1 medium onion chopped']]),
				'imgUrl' => $this->images[$k],
				'description' => $this->stepByStepWithGifTemplate(),
				'cookbook_id' => $this->cookbooks[$random_cookbookId],
				'user_id' => $this->users[$random_userId],
				'summary' => $faker->sentence(20),
				'calorie_count' => 0,
				'cook_time' => '2020-07-09 01:45:00',
				'prep_time' => '2020-07-09 00:10:00',
				'nutritional_detail' => json_encode(['cal' => '462', 'carbs' => '42', 'protein' => '43', 'fat' => '28']),
				'servings' => rand(1,10),
				'resource_type' => 'recipe',
				'cuisine' => $cuisine[$random_cuisine],
				'course' => $course[$random_course]
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
