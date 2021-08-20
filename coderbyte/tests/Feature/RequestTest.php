<?php

namespace Tests\Feature;

use App\Models\Request;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RequestTest extends TestCase
{

    public function test_it_get_all_request()
    {
        $user = User::factory()->create();
        $request = Request::factory(10)->create();

        $this->jsonAs($user, 'GET', 'api/request/index')
            ->assertJsonCount(10, 'data');
            
    }

    public function test_it_get_all_request_for_product_owner()
    {
        $user = User::factory()->create();
        $request = Request::factory()->create([
            'owner_id' => $user->id
        ]);

        $this->jsonAs($user, 'GET', 'api/request/indexProductOwner/'.$user->id)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_get_all_assignment_for_photographer()
    {
        $user = User::factory()->create();
        $request = Request::factory()->create([
            'photographer_id' => $user->id
        ]);

        $this->jsonAs($user, 'GET', 'api/request/indexPhotographer/'.$user->id)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_create_a_request_by_product_owner()
    {
        $user = User::factory()->create();
        $data = [
            'owner_id' => $user->id,
            'product' => 'Houses',
            'location' => 'Lagos'
        ];

        $this->jsonAs($user, 'POST', 'api/request/create', $data)
            ->assertJsonFragment([
                'location' => 'Lagos',
            ]);
            
    }

    public function test_it_assign_a_request_to_photographer_by_admin()
    {
        $user = User::factory()->create([
            'category' => 1
        ]);
        $request = Request::factory()->create();
        $data = [
            'photographer_id' => $user->id
        ];

        $this->jsonAs($user, 'POST', 'api/request/assign/'.$request->id, $data)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_uploading_images_by_photographer()
    {
        $user = User::factory()->create([
            'category' => 3
        ]);
        $request = Request::factory()->create([
            'photographer_id' => $user->id,
            'owner_id' => $user->id //this line was included because it send a mail to the product owner
        ]);
        $data = [
            'lqt' => 'https://image.com/thumb',
            'hri' => 'https://image.com'
        ];

        $this->jsonAs($user, 'POST', 'api/request/upload/'.$request->id, $data)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_for_approving_photographers_image_by_product_owner()
    {
        $user = User::factory()->create([
            'category' => 2
        ]);
        $request = Request::factory()->create([
            'photographer_id' => $user->id //this line was included because it send a mail to the product owner
        ]);

        $this->jsonAs($user, 'GET', 'api/request/approve/'.$request->id)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_for_rejecting_photographers_image_by_product_owner()
    {
        $user = User::factory()->create([
            'category' => 2
        ]);
        $request = Request::factory()->create([
            'photographer_id' => $user->id //this line was included because it send a mail to the product owner
        ]);

        $this->jsonAs($user, 'GET', 'api/request/reject/'.$request->id)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_for_deleting_request_by_product_owner()
    {
        $user = User::factory()->create([
            'category' => 2
        ]);
        $request = Request::factory()->create();

        $this->jsonAs($user, 'GET', 'api/request/delete/'.$request->id)
            ->assertJsonFragment([
                'status' => 'Success',
            ]);
            
    }

    public function test_it_for_editing_request_by_product_owner()
    {
        $user = User::factory()->create([
            'category' => 2
        ]);
        $request = Request::factory()->create();
        $data = [
            'product' => $request->product,
            'location' => $request->location,
        ];

        $this->jsonAs($user, 'POST', 'api/request/edit-request/'.$request->id, $data)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }

    public function test_it_for_editing_request_photographers_image_by_photographer()
    {
        $user = User::factory()->create([
            'category' => 3
        ]);
        $request = Request::factory()->create();
        $data = [
            'lqt' => 'https://image.com/quality-low',
            'hri' => 'https://image.com/quality-high'
        ];

        $this->jsonAs($user, 'POST', 'api/request/edit-request-image/'.$request->id, $data)
            ->assertJsonFragment([
                'product' => $request->product,
                'location' => $request->location,
            ]);
            
    }
}
