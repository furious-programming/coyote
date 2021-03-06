<?php

namespace Coyote;

class TagCreationValidator
{
    public function validateTag($attribute, $value, $parameters, $validator)
    {
        $requiredReputation = $parameters[0]; // required reputation points
        $userReputation = auth()->guest() ? 0 : auth()->user()->reputation;

        if ($userReputation >= $requiredReputation) {
            return true;
        }

        $tag = Tag::where('name', $value)->pluck('id');
        if ($tag) {
            return true;
        }

        return false;
    }
}
