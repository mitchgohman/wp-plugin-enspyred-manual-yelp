import styled from "styled-components";

const EmbedContainer = styled.div`
    width: 100%;
    position: relative;
`;

const IframeScaler = styled.div`
    width: 100%;
    height: 350px;
    position: relative;
    overflow: hidden;

    iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        max-width: none;
        height: 100%;
        border: 0;
    }
`;

const ErrorMessage = styled.div`
    padding: 20px;
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    color: #721c24;
    text-align: center;
`;

const YelpReviewEmbed = ({ reviewId, title }) => {
    if (!reviewId) {
        return (
            <ErrorMessage>
                <strong>Error:</strong> Missing Review ID for "
                {title || "Unknown Review"}"
            </ErrorMessage>
        );
    }

    return (
        <EmbedContainer>
            <IframeScaler>
                <iframe
                    src={`https://www.yelp.com/embed/review/${reviewId}`}
                    loading="lazy"
                    referrerPolicy="no-referrer-when-downgrade"
                    allowFullScreen
                    title={title || "Yelp Review"}
                />
            </IframeScaler>
        </EmbedContainer>
    );
};

export default YelpReviewEmbed;
