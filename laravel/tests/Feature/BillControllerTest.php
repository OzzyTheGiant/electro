<?php

namespace Tests;

use Tests\TestCase;

class BillControllerTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_bills_can_be_fetched(): void {
        $response = $this->get('/api/bills');
        $response->assertStatus(200);
        $response->assertJson([[
            "id" => 1,
            "user_id" => 1,
            "payment_amount" => 50.99,
            "payment_date" => "2022-01-01"
        ]]);
    }

    public function test_bills_can_be_added(): void {
        $response = $this->post('/api/bills', [
            "user_id" => 1,
            "payment_amount" => 60.99,
            "payment_date" => "2022-02-01"
        ], [
            "Accept" => "application/json"
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            "id" => 2,
            "user_id" => 1,
            "payment_amount" => 60.99,
            "payment_date" => "2022-02-01"
        ]);
    }

    public function test_error_422_when_missing_required_bill_data_for_creation(): void {
        $response = $this->post('/api/bills', [], ["Accept" => "application/json"]);
        $response->assertStatus(422);
    }


    public function test_bills_can_be_edited(): void {
        $response = $this->put('/api/bills/1', [
            "user_id" => 1,
            "payment_amount" => 70.99,
            "payment_date" => "2022-02-01"
        ], [
            "Accept" => "application/json"
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            "id" => 1,
            "user_id" => 1,
            "payment_amount" => 70.99,
            "payment_date" => "2022-02-01"
        ]);
    }

    public function test_bills_can_be_deleted(): void {
        $response = $this->delete('/api/bills/1');
        $response->assertStatus(204);
    }


    public function test_error_404_when_specified_bill_is_missing(): void {
        $response = $this->delete('/api/bills/5');
        $response->assertStatus(404);
    }
}
