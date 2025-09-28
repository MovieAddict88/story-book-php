package com.storyapp.kids.models;

import com.google.gson.annotations.SerializedName;
import java.util.List;

public class Story {
    @SerializedName("id")
    private int id;

    @SerializedName("title")
    private String title;

    @SerializedName("description")
    private String description;

    @SerializedName("cover_image")
    private String coverImage;

    @SerializedName("pages")
    private List<StoryPage> pages;

    // Getters
    public int getId() {
        return id;
    }

    public String getTitle() {
        return title;
    }

    public String getDescription() {
        return description;
    }

    public String getCoverImage() {
        return coverImage;
    }

    public List<StoryPage> getPages() {
        return pages;
    }

    // Setters
    public void setId(int id) {
        this.id = id;
    }

    public void setTitle(String title) {
        this.title = title;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public void setCoverImage(String coverImage) {
        this.coverImage = coverImage;
    }

    public void setPages(List<StoryPage> pages) {
        this.pages = pages;
    }
}