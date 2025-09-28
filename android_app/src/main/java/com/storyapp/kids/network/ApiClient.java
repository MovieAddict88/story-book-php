package com.storyapp.kids.network;

import retrofit2.Retrofit;
import retrofit2.converter.gson.GsonConverterFactory;

public class ApiClient {

    // IMPORTANT: Replace with your actual server IP or domain.
    // Using "10.0.2.2" for the Android emulator to access the host machine's localhost.
    private static final String BASE_URL = "http://10.0.2.2/story_app/backend/api/";

    private static Retrofit retrofit = null;

    /**
     * Returns a singleton instance of Retrofit.
     * @return The Retrofit instance.
     */
    public static Retrofit getClient() {
        if (retrofit == null) {
            retrofit = new Retrofit.Builder()
                    .baseUrl(BASE_URL)
                    .addConverterFactory(GsonConverterFactory.create())
                    .build();
        }
        return retrofit;
    }

    /**
     * A convenience method to get the ApiService interface.
     * @return An implementation of the ApiService interface.
     */
    public static ApiService getApiService() {
        return getClient().create(ApiService.class);
    }
}