<?php

declare(strict_types=1);

namespace TimLappe\Elephactor\Domain\Php\Analysis\Model\Name;

enum NameKind
{
    case NamespaceDecleration;
    case UseStatement;
    case ClassLikeDeclaration;
    case Attribute;
    case ClassExtends;
    case InterfaceExtends;
    case InterfaceImplementation;
    case Property;
    case TraitUsage;
    case ClassLikeUsage;
    case Unknown;
}