package com.storyapp.kids;

import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;

import com.bumptech.glide.Glide;

public class StoryPageFragment extends Fragment {

    private static final String ARG_IMAGE_URL = "arg_image_url";
    private static final String ARG_PAGE_TEXT = "arg_page_text";

    private String imageUrl;
    private String pageText;

    public static StoryPageFragment newInstance(String imageUrl, String pageText) {
        StoryPageFragment fragment = new StoryPageFragment();
        Bundle args = new Bundle();
        args.putString(ARG_IMAGE_URL, imageUrl);
        args.putString(ARG_PAGE_TEXT, pageText);
        fragment.setArguments(args);
        return fragment;
    }

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if (getArguments() != null) {
            imageUrl = getArguments().getString(ARG_IMAGE_URL);
            pageText = getArguments().getString(ARG_PAGE_TEXT);
        }
    }

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        return inflater.inflate(R.layout.fragment_story_page, container, false);
    }

    @Override
    public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        ImageView imageView = view.findViewById(R.id.imageView_page);
        TextView textView = view.findViewById(R.id.textView_page_text);

        textView.setText(pageText);

        if (imageUrl != null && !imageUrl.isEmpty()) {
            Glide.with(this)
                 .load(imageUrl)
                 .placeholder(R.color.pastel_green)
                 .into(imageView);
        } else {
            imageView.setVisibility(View.GONE);
        }
    }
}