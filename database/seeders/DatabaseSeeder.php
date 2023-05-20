<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    protected $users;

    protected $cookbooks;

    protected $recipes;

    protected $images = [
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/3.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/4.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/5.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/6.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/beginners.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/corn.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/kebab.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/nigeria-party-food.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/vegan.jpg',
        'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/wraps.jpg',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     *
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
                $admin = new \App\Models\User([
                    'name' => 'Florence Okosun',
                    'email' => 'okosunuzflorence@gmail.com',
                    'password' => Hash::make('0B10r@.UM3h'),
                    'followers' => 0,
                    'following' => 0,
                    'avatar' => '',
                    'pronouns' => 'She/Her',
                    'expertise_level' => 'Founder',
                    'can_take_orders' => false,
                    'about' => '',
                    'email_verified' => '2020-01-01 00:00:00',
                    'name_slug' => 'florence-okosun',
                ]);
                $admin->save();
                $this->createContactDetail($admin);

                $editor = new \App\Models\User([
                    'name' => 'Tony Udomaye',
                    'email' => 'udomiayetony@gmail.com',
                    'password' => Hash::make('secret'),
                    'followers' => 0,
                    'following' => 0,
                    'avatar' => 'https://ca.slack-edge.com/T5QPN806A-U01A3835GPP-72238718978f-512',
                    'pronouns' => 'He/Him',
                    'expertise_level' => 'VP Product Engineering',
                    'can_take_orders' => false,
                    'about' => '',
                    'email_verified' => '2020-01-01 00:00:00',
                    'name_slug' => 'tony-udomaiye',
                ]);
                $editor->save();
                $this->createContactDetail($editor);

                $contributor = new \App\Models\User([
                    'name' => 'Test user',
                    'email' => 'test@somemail.com',
                    'password' => Hash::make('secret'),
                    'followers' => 0,
                    'following' => 0,
                    'avatar' => '',
                    'pronouns' => 'They/Them',
                    'expertise_level' => 'Freelancer',
                    'can_take_orders' => true,
                    'about' => '',
                    'email_verified' => null,
                    'name_slug' => 'test-user',
                ]);

                $contributor->save();
                $this->createContactDetail($contributor);

                //create cookbooks for canada, us and african countries. dont add recipes to any
                $cookbook = new \App\Models\Cookbook([
                    'name' => 'Nigerian Party Food (Owambe)',
                    'description' => 'A collection of common Nigerian Party foods, everything from Jollof rice to swallows and soups to mention a few. This cookbook may contain contributions from multiple contributors and content thereof belongs to cookbookhq. Dive right in to browse different Nigerian party food recipes.',
                    'bookCoverImg' => 'https://cookbookshq.s3.us-east-2.amazonaws.com/cookbooks-cover-photos/nigeria-party-food.jpg',
                    'flag_id' => 1,
                    'slug' => slugify('Nigerian Party Food (Owambe)'),
                    'category_id' => 1,
                    'user_id' => $admin->id,
                    'resource_type' => 'cookbook',
                    'created_at' => new \DateTime(),
                    'updated_at' => new \DateTime(),
                    'alt_text' => '5 trays containing nigerian party food arranged in one line',
                ]);

                $cookbook->save();

                $cookbook->users()->attach($editor->getKey());
                $cookbook->users()->attach($contributor->getKey());

                $categoryIds = \App\Models\Category::all()->pluck("id")->toArray();

                foreach ($categoryIds as $cat_id) {
                    $cookbook->categories()->attach($cat_id);
                }
            }

            //fakes
            $faker = app(\Faker\Generator::class);

            $this->createUsers($faker);
            $this->createCookbooks($faker);
            $this->createRecipes($faker);
        });

        //create one authorized client
        $authorized_client = new \App\Models\AuthorizedClient([
            'api_key' => 'SijjocvGGcgnXVbXzSoVtmN5qPor0jl8PnlRJ25U26JCODpoyi',
            'client_secret' => 'eyJpdiI6Iko2Ujhya1lBN3ZxeHRKV3JMK2I2NWc9PSIsInZhbHVlIjoiKzhaaE9mckY3V0RDN1ljT2lNT1pheFdLKzJcL2pTOTMwS2ZMcmc5aitQenlVV3hqbytZRkpGeXgyS09CSmpFQXZcL1hDSGFtOWhrNWF3bzdqMU9hNFVudjJvSnZLZk5GOVI2T3R5ZjNQeXkrTT0iLCJtYWMiOiIyOGVmYTZkZjMyZDA3M2MzMzdkZDg0ZDI5Zjg0Y2MzYzliMmM0MGZjNTcxMDliMDk2ZjQ5M2VjYzFkZGFmZTEzIn0=',
            'passphrase' => 'kpfSpywUdY',
        ]);

        $authorized_client->save();
    }

    /**
     * creates users
     */
    private function createUsers($faker)
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
            'https://bit.ly/3cgr09f',
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
            'Care giver',
        ];

        for ($j = 0; $j < 20; $j++) {
            $rand_level = array_rand($levels);

            $user = new \App\Models\User([
                'name' => $faker->firstName.' '.$faker->lastName,
                'email' => $faker->email,
                'password' => Hash::make('secret'),
                'followers' => 0,
                'following' => 0,
                'avatar' => $avatars[$j],
                'pronouns' => $pronouns[rand(0, 2)],
                'expertise_level' => $levels[$rand_level],
                'can_take_orders' => array_rand($can_take_orders),
                'about' => $faker->paragraph(6),
                'email_verified' => '2020-01-01 00:00:00',
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
     */
    private function createCookbooks($faker)
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
            'European Food',
        ];

        for ($i = 0; $i < 10; $i++) {
            $flag_ids = range(2, 35);
            $lucky_flagId_spin = array_rand($flag_ids);
            $flagId = $flag_ids[$lucky_flagId_spin];

            $cookbook = new \App\Models\Cookbook([
                'name' => $sample_titles[$i],
                'description' => $faker->sentence(150),
                'bookCoverImg' => $this->images[$i],
                'flag_id' => $flagId,
                'user_id' => $random_author_id,
                'resource_type' => 'cookbook',
                'created_at' => new \DateTime(),
                'updated_at' => new \DateTime(),
                'alt_text' => 'sample image',
            ]);

            $cookbook->slug = slugify($cookbook->name);

            $cookbook->save();
            $random_users = array_rand($this->users, rand(2, 5));

            $categories = \App\Models\Category::all();

            $categoryIds = $categories->map(function ($item) {
                return $item->id;
            });

            $categoryIds = $categoryIds->toArray();

            $random_categories = array_rand($categoryIds, rand(2, 5));

            foreach ($random_users as $key => $val) {
                $cookbook->users()->attach($val);
            }

            foreach ($random_categories as $key => $val) {
                $cookbook->category_id = $val;
                $cookbook->save();
            }

            $category_ids = range(1, 10);
            $random_category_ids = array_rand($category_ids, 2);

            foreach ($random_category_ids as $key => $val) {
                if ($val == 0) {
                    continue;
                }
                //				$cookbook->categories()->attach($val);
            }

            $cookbooks[] = $cookbook->id;
        }

        $this->cookbooks = $cookbooks;
    }

    /**
     * Creates Recipes
     */
    private function createRecipes($faker)
    {
        $cuisine = [
            'chinese',
            'mexican',
            'african',
            'french',
            'italian',
            'thai',
            'spanish',
            'indian',
        ];

        $course = [
            'main',
            'side',
            'dessert',
        ];

        $recipe = null;
        $recipes = [];

        for ($k = 0; $k < 10; $k++) {
            $random_cookbookId = array_rand($this->cookbooks);
            $random_userId = array_rand($this->users);
            $random_cuisine = array_rand($cuisine);
            $random_course = array_rand($course);

            $recipe = new \App\Models\Recipe([
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
                'servings' => rand(1, 10),
                'resource_type' => 'recipe',
                'cuisine' => $cuisine[$random_cuisine],
                'course' => $course[$random_course],
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
     * @param  \App\Models\Recipe  $recipe
     */
    private function createVariation($faker, \App\Models\Recipe $recipe)
    {
        $variation = new \App\Models\RecipeVariation([
            'name' => $faker->word,
            'ingredients' => json_encode(['data' => ['2 lbs greek yoghurt']]),
            'imgUrl' => $faker->imageUrl(),
            'description' => $this->stepByStepWithGifTemplate(),
            'recipe_id' => $recipe->id,
            'resource_type' => 'recipe_variation',
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
     *
     * @param  \App\Models\User  $user
     */
    private function createContactDetail(\App\Models\User $user)
    {
        $contact = new \App\Models\UserContactDetail([
            'user_id' => $user->id,
            'visibility' => 'public',
            'phone' => '(647) 000 0000',
            'facebook' => 'https://facebook.com/test-user',
            'twitter' => 'https://twitter.com/test-user',
            'instagram' => 'https://instagram.com/test-user',
            'skype' => '@test-user',
            'calendly' => 'https://calendly.com/test-user',
            'office_address' => '560 Tim Hortons Blvd ON M5V 3M3',
            'website' => 'www.mywebsite.com',
        ]);
        $contact->save();
    }

    /**
     * Create Verified user
     *
     * @param  \App\Models\User  $user
     */
    private function createEmailVerification(\App\Models\User $user)
    {
        $verification = new \App\Models\EmailVerification([
            'user_id' => $user->id,
            'token' => \Illuminate\Support\Facades\Crypt::encrypt(['user_id' => $user->id, 'email' => $user->email, 'secret' => env('CRYPT_SECRET')]),
            'is_verified' => \Carbon\Carbon::now(),
        ]);
        $verification->save();
    }
}
