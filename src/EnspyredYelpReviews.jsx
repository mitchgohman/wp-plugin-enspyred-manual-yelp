/* global __PLUGIN_VERSION__ */
import React from "react";
import styled from "styled-components";
import ReviewCard from "./core/components/ReviewCard";

const Container = styled.div`
    max-width: 100%;
    margin: 0 auto;
    font-family:
        -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue",
        Arial, sans-serif;
`;

const ReviewsGrid = styled.div`
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;

    @media (max-width: 768px) {
        grid-template-columns: 1fr;
    }
`;

const NoReviews = styled.div`
    padding: 40px;
    text-align: center;
    color: #666;
    font-size: 16px;
`;

const EnspyredYelpReviews = ({ reviews, gallery }) => {
    if (!reviews || reviews.length === 0) {
        return (
            <Container>
                <NoReviews>No reviews available for this gallery.</NoReviews>
            </Container>
        );
    }

    return (
        <Container data-version={__PLUGIN_VERSION__}>
            <ReviewsGrid>
                {reviews.map((review) => (
                    <ReviewCard key={review.id} review={review} />
                ))}
            </ReviewsGrid>
        </Container>
    );
};

export default EnspyredYelpReviews;
