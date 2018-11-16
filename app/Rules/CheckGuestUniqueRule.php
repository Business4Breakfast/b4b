<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckGuestUniqueRule implements Rule
{


    private $club_id;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        $this->club_id = null;

        dump($attribute);
        dump($value);

//
//        $matches = PhoneBook::whereFirstName(request(first_name))
//            ->whereLastName(request(last_name))
//            ->whereAddress(request(address))
//            ->count();

        $matches = 1;

        $this->club_id = 'test variable';

        return $matches === 0;


    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Vyzerá že  ' . $this->club_id . '  :attribute tento hosť už existuje.';
    }
}
