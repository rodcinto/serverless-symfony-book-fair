<?php

namespace App\Entity\Member;

enum Role: string
{
  case ORGANIZER = 'ROLE_ORGANIZER';
  case AUTHOR = 'ROLE_AUTHOR';
  case GUEST = 'ROLE_GUEST';
}
