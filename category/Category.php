<?php
namespace Category;

class Category{
    public static function clouse_category_on_new_post()
    {

        add_filter("widget_categories_args", "clouse");
    }

    private static function clouse($args)
    {

            $exclude = 2;
            $args["exclude"] = $exclude;
            return $args;

    }
}