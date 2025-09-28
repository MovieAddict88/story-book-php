package com.storyapp.kids.models;

import com.google.gson.annotations.SerializedName;

public class StoryPage {
    @SerializedName("id")
    private int id;

    @SerializedName("page_number")
    private int pageNumber;

    @SerializedName("text")
    private String text;

    @SerializedName("image")
    private String image;

    @SerializedName("audio")
    private Audio audio;

    // Getters
    public int getId() {
        return id;
    }

    public int getPageNumber() {
        return pageNumber;
    }

    public String getText() {
        return text;
    }

    public String getImage() {
        return image;
    }

    public Audio getAudio() {
        return audio;
    }

    // Setters
    public void setId(int id) {
        this.id = id;
    }

    public void setPageNumber(int pageNumber) {
        this.pageNumber = pageNumber;
    }

    public void setText(String text) {
        this.text = text;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public void setAudio(Audio audio) {
        this.audio = audio;
    }
}