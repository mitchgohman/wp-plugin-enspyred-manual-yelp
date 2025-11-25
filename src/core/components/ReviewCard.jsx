import React from "react";
import styled from "styled-components";
import StarRating from "./StarRating";

const Card = styled.div`
    border: 3px solid #d32323;
    border-radius: 4px;
    padding: 16px;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    gap: 12px;
`;

const BusinessHeader = styled.div`
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding-bottom: 12px;
    border-bottom: 1px solid #e6e6e6;
`;

const BusinessInfo = styled.div`
    flex: 1;
`;

const BusinessName = styled.a`
    font-size: 18px;
    font-weight: bold;
    color: #0073bb;
    text-decoration: none;
    display: block;
    margin-bottom: 4px;

    &:hover {
        text-decoration: underline;
    }
`;

const BusinessMeta = styled.div`
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
`;

const YelpLogo = styled.img`
    width: 60px;
    height: auto;
`;

const ReviewerSection = styled.div`
    display: flex;
    align-items: flex-start;
    gap: 12px;
`;

const ReviewerPhoto = styled.img`
    width: 60px;
    height: 60px;
    border-radius: 4px;
    object-fit: cover;
`;

const ReviewerInfo = styled.div`
    flex: 1;
`;

const ReviewerName = styled.div`
    font-size: 16px;
    font-weight: bold;
    color: #d32323;
    margin-bottom: 4px;
`;

const ReviewerMeta = styled.div`
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #666;
`;

const MetaItem = styled.div`
    display: flex;
    align-items: center;
    gap: 4px;
`;

const Icon = styled.span`
    color: #f15c00;
`;

const ReviewRating = styled.div`
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 8px;
`;

const ReviewDate = styled.span`
    font-size: 14px;
    color: #666;
`;

const ReviewText = styled.p`
    font-size: 14px;
    line-height: 1.6;
    color: #333;
    margin: 0;
`;

const ReadMoreLink = styled.a`
    display: inline-block;
    margin-top: 8px;
    font-size: 14px;
    color: #0073bb;
    text-decoration: none;
    font-weight: 500;

    &:hover {
        text-decoration: underline;
    }
`;

const ReviewCard = ({ review }) => {
    return (
        <Card>
            <BusinessHeader>
                <BusinessInfo>
                    <BusinessName
                        href={review.businessUrl}
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {review.businessName}
                    </BusinessName>
                    <BusinessMeta>
                        <StarRating rating={review.businessRating} />
                        <span>{review.businessReviewCount} reviews</span>
                    </BusinessMeta>
                </BusinessInfo>
                <YelpLogo
                    src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNzIiIGhlaWdodD0iMzYiIHZpZXdCb3g9IjAgMCA3MiAzNiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTI1LjMgMTcuNkMyNC45IDE3LjYgMjQuNSAxNy43IDI0LjIgMTcuOUwyMS45IDE5LjJDMjEuNiAxOS4zIDIxLjMgMTkuNCAyMSAxOS40QzIwLjQgMTkuNCAyMCAxOSAyMCAxOC40VjkuNEMyMCA4LjggMjAuNCA4LjQgMjEgOC40QzIxLjMgOC40IDIxLjYgOC41IDIxLjkgOC43TDI0LjIgMTBDMjQuNSAxMC4yIDI0LjkgMTAuMyAyNS4zIDEwLjNIMzIuM0MzMi45IDEwLjMgMzMuMyAxMC43IDMzLjMgMTEuM1YxNi42QzMzLjMgMTcuMiAzMi45IDE3LjYgMzIuMyAxNy42SDI1LjNaIiBmaWxsPSIjRDMyMzIzIi8+CjxwYXRoIGQ9Ik00NC42IDEzLjhINTIuMkM1Mi42IDEzLjggNTIuOSAxNC4xIDUyLjkgMTQuNVYxOS44QzUyLjkgMjAuMiA1Mi42IDIwLjUgNTIuMiAyMC41SDQ0LjZDNDQuMiAyMC41IDQzLjkgMjAuMiA0My45IDE5LjhWMTQuNUM0My45IDE0LjEgNDQuMiAxMy44IDQ0LjYgMTMuOFoiIGZpbGw9IiNEMzIzMjMiLz4KPHBhdGggZD0iTTU4LjEgOS45TDU0IDkuNkM1My41IDkuNiA1My4yIDkuMiA1My4yIDguN0w1My4xIDQuNkM1My4xIDQuMSA1My40IDMuOCA1My45IDMuOEw1OC4xIDMuOUM1OC42IDMuOSA1OC45IDQuMiA1OC45IDQuN0w1OSA4LjhDNTkgOS4zIDU4LjYgOS45IDU4LjEgOS45WiIgZmlsbD0iI0QzMjMyMyIvPgo8cGF0aCBkPSJNMjQuMiAyNy40TDIxLjkgMjYuMUMyMS42IDI1LjkgMjEuMyAyNS44IDIxIDI1LjhDMjAuNCAyNS44IDIwIDI2LjIgMjAgMjYuOFYzNS44QzIwIDM2LjQgMjAuNCAzNi44IDIxIDM2LjhDMjEuMyAzNi44IDIxLjYgMzYuNyAyMS45IDM2LjVMMjQuMiAzNS4yQzI0LjUgMzUgMjQuOSAzNC45IDI1LjMgMzQuOUgzMi4zQzMyLjkgMzQuOSAzMy4zIDM0LjUgMzMuMyAzMy45VjI4LjZDMzMuMyAyOCAzMi45IDI3LjYgMzIuMyAyNy42SDI1LjNDMjQuOSAyNy42IDI0LjUgMjcuNSAyNC4yIDI3LjRaIiBmaWxsPSIjRDMyMzIzIi8+Cjwvc3ZnPg=="
                    alt="Yelp"
                />
            </BusinessHeader>

            <ReviewerSection>
                <ReviewerPhoto
                    src={review.reviewerPhotoUrl}
                    alt={review.reviewerName}
                    onError={(e) => {
                        // Prevent infinite loop by only setting fallback once
                        if (!e.target.dataset.fallback) {
                            e.target.dataset.fallback = "true";
                            // Use a simple gray placeholder as fallback
                            e.target.src =
                                'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="60" height="60"%3E%3Crect width="60" height="60" fill="%23ddd"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999" font-family="Arial" font-size="24"%3E%3F%3C/text%3E%3C/svg%3E';
                        }
                    }}
                />
                <ReviewerInfo>
                    <ReviewerName>{review.reviewerName}</ReviewerName>
                    <ReviewerMeta>
                        <MetaItem>
                            <Icon>👥</Icon>
                            <span>{review.reviewerFriendsCount}</span>
                        </MetaItem>
                        <MetaItem>
                            <Icon>⭐</Icon>
                            <span>{review.reviewerReviewCount}</span>
                        </MetaItem>
                    </ReviewerMeta>
                </ReviewerInfo>
            </ReviewerSection>

            <ReviewRating>
                <StarRating rating={review.rating} size="large" />
                <ReviewDate>{review.date}</ReviewDate>
            </ReviewRating>

            <ReviewText>{review.text}</ReviewText>

            <ReadMoreLink
                href={review.yelpUrl}
                target="_blank"
                rel="noopener noreferrer"
            >
                Read more on Yelp
            </ReadMoreLink>
        </Card>
    );
};

export default ReviewCard;
