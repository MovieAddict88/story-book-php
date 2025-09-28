package com.storyapp.kids.network;

import com.storyapp.kids.models.Category;
import com.storyapp.kids.models.Language;
import com.storyapp.kids.models.Story;

import java.util.List;

import retrofit2.Call;
import retrofit2.http.GET;
import retrofit2.http.Query;

public interface ApiService {

    /**
     * Fetches a list of all available languages.
     */
    @GET("languages.php")
    Call<List<Language>> getLanguages();

    /**
     * Fetches a list of all available categories.
     */
    @GET("categories.php")
    Call<List<Category>> getCategories();

    /**
     * Fetches a list of stories. Can be filtered by language and/or category.
     * @param languageId The ID of the language to filter by (optional).
     * @param categoryId The ID of the category to filter by (optional).
     */
    @GET("stories.php")
    Call<List<Story>> getStories(@Query("language_id") Integer languageId, @Query("category_id") Integer categoryId);

    /**
     * Fetches the full details for a single story, including its pages.
     * @param storyId The ID of the story to fetch.
     */
    @GET("story_details.php")
    Call<Story> getStoryDetails(@Query("id") int storyId);
}