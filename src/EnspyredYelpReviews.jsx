/* global __PLUGIN_VERSION__ */
import styled from "styled-components";
import YelpReviewEmbed from "./core/components/YelpReviewEmbed";

const Container = styled.div`
    max-width: 100%;
    margin: 0 auto;
    font-family:
        -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue",
        Arial, sans-serif;
`;

const ReviewsGrid = styled.div`
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    margin-top: 20px;
    max-width: 1140px;
    margin: 0 auto;

    > * {
        /* Mobile: 1 column - full width */
        flex: 1 1 100%;
    }

    @media (min-width: 768px) {
        > * {
            /* Tablet: 2 columns - fill width */
            flex: 1 1 calc(50% - 12px);
        }
    }

    @media (min-width: 1600px) {
        > * {
            /* Desktop: 4 columns - fill width */
            /* flex: 1 1 calc(25% - 18px); */
        }
    }

    @media (min-width: 2000px) {
        > * {
            /* Large screen: 5 columns - fill width */
            /* flex: 1 1 calc(20% - 19.2px); */
        }
    }
`;

const NoReviews = styled.div`
    padding: 40px;
    text-align: center;
    color: #666;
    font-size: 16px;
`;

const EnspyredYelpReviews = ({ reviews }) => {
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
                {reviews.map((review, index) => (
                    <YelpReviewEmbed
                        key={review.reviewId || index}
                        reviewId={review.reviewId}
                        title={review.title}
                    />
                ))}
            </ReviewsGrid>
        </Container>
    );
};

export default EnspyredYelpReviews;
